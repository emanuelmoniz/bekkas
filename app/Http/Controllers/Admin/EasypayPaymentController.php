<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EasypayPayment;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class EasypayPaymentController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of Easypay payments (admin).
     */
    public function index(Request $request)
    {
        $query = EasypayPayment::with(['order.user', 'checkoutSession']);

        if ($request->filled('order_number')) {
            $query->whereHas('order', function ($q) use ($request) {
                $q->where('order_number', 'like', '%'.$request->order_number.'%');
            });
        }

        if ($request->filled('from_paid_date')) {
            // allow filtering by paid_at (preferred) or fall back to created_at for older rows
            $query->where(function ($q) use ($request) {
                $q->whereDate('paid_at', '>=', $request->from_paid_date)
                    ->orWhereDate('created_at', '>=', $request->from_paid_date);
            });
        }

        if ($request->filled('to_paid_date')) {
            $query->where(function ($q) use ($request) {
                $q->whereDate('paid_at', '<=', $request->to_paid_date)
                    ->orWhereDate('created_at', '<=', $request->to_paid_date);
            });
        }

        $payments = $query->latest('created_at')->get();

        return view('admin.orders.payments.index', compact('payments'));
    }

    /**
     * Display a single Easypay payment (admin).
     */
    public function show(EasypayPayment $payment)
    {
        $payment->load(['order.user', 'checkoutSession']);

        return view('admin.orders.payments.show', ['payment' => $payment]);
    }

    /**
     * Admin: trigger a refund request against Easypay for a given payment id.
     * Does NOT mutate local order/payment state — webhook will notify actual outcome.
     */
    public function refund(\Illuminate\Http\Request $request, EasypayPayment $payment)
    {
        $payment->load('order');

        // Authorization: reuse Order policy
        $this->authorize('refund', $payment->order);

        if (strtolower((string) $payment->payment_status) !== 'paid' || ! optional($payment->order)->is_paid) {
            return redirect()->back()->with('error', 'Refund could not be processed.');
        }

        $value = (float) optional($payment->order)->total_gross ?: 0.0;

        try {
            $svc = app(\App\Services\EasypayService::class);

            // Prefer capture_id when available (refunds operate on captures when present)
            $idToUse = $payment->capture_id ?: $payment->payment_id ?: (string) $payment->id;

            $resp = $svc->refundSinglePayment($idToUse, $value);

            if (is_array($resp) && isset($resp['status']) && (int) $resp['status'] === 201) {
                // Persist refund id when supplied by Easypay
                $refundId = data_get($resp, 'body.id') ?? data_get($resp, 'body.refund_id') ?? data_get($resp, 'body', [])['id'] ?? null;
                if (! empty($refundId)) {
                    try {
                        $payment->refund_id = (string) $refundId;
                        $payment->save();
                    } catch (\Throwable $e) {
                        logger()->warning('easypay.refund.persist_failed', ['payment_id' => $payment->id, 'refund_id' => $refundId, 'err' => $e->getMessage()]);
                    }
                }

                return redirect()->back()->with('success', 'Refund request was submited');
            }

            // Explicit fallback for any non-201 response
            logger()->info('easypay.refund.response', ['resp' => $resp, 'id_used' => $idToUse]);
            session()->flash('error', 'Refund could not be processed.');

            return redirect()->back();
        } catch (\Throwable $e) {
            logger()->warning('Admin refund request failed', ['payment_id' => $payment->id, 'err' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Refund could not be processed.');
        }
    }

    /**
     * Admin: refresh a single payment from Easypay's single-payment endpoint and
     * update local EasypayPayment + related Order state when appropriate.
     */
    public function refresh(EasypayPayment $payment)
    {
        $payment->load('order');

        if (empty($payment->payment_id)) {
            return redirect()->back()->with('error', 'Payment has no payment_id to fetch');
        }

        try {
            $svc = app(\App\Services\EasypayService::class);
            $single = $svc->getSinglePayment($payment->payment_id);
        } catch (\Throwable $e) {
            logger()->warning('Admin payment refresh failed', ['payment_id' => $payment->id, 'err' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Failed to fetch payment details');
        }

        if (empty($single) || ! is_array($single)) {
            return redirect()->back()->with('error', 'No remote payment data available');
        }

        // Map remote attributes to local model (keep shape similar to SDK/controller logic)
        $attrs = [
            'payment_status' => data_get($single, 'payment_status') ?? data_get($single, 'payment.status') ?? $payment->payment_status,
            'paid_at' => data_get($single, 'paid_at') ? \Carbon\Carbon::parse(data_get($single, 'paid_at')) : $payment->paid_at,
            'mb_entity' => data_get($single, 'method.entity') ?? data_get($single, 'payment.entity') ?? $payment->mb_entity,
            'mb_reference' => data_get($single, 'method.reference') ?? data_get($single, 'payment.reference') ?? $payment->mb_reference,
            'mb_expiration_time' => data_get($single, 'multibanco.expiration_time') ? \Carbon\Carbon::parse(data_get($single, 'multibanco.expiration_time')) : $payment->mb_expiration_time,
            'iban' => data_get($single, 'method.sdd_mandate.iban') ?? data_get($single, 'method.sdd_mandate') ?? $payment->iban,
            'capture_id' => data_get($single, 'captures.0.id') ?? $payment->capture_id,
            'raw_response' => $single,
        ];

        try {
            $payment->update($attrs);
            $payment->refresh();
        } catch (\Throwable $e) {
            logger()->warning('Admin payment refresh: could not persist remote response', ['payment_id' => $payment->id, 'err' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Failed to persist payment details');
        }

        // If remote reports paid and order exists, mark order as paid (idempotent)
        $remoteStatus = data_get($single, 'payment_status') ?? data_get($single, 'payment.status');
        if ($payment->order && $remoteStatus === 'paid') {
            try {
                $payment->order->markAsPaid('easypay', ['payment_id' => $payment->payment_id]);
            } catch (\Throwable $e) {
                logger()->warning('Admin payment refresh: could not mark order paid', ['order_id' => $payment->order->id, 'err' => $e->getMessage()]);
            }
        }

        return redirect()->back()->with('success', 'Payment details refreshed');
    }

    /**
     * Admin: refresh refund details from Easypay's refund endpoint and persist
     * the response into `raw_response.refund` for debugging/inspection.
     */
    public function refreshRefund(EasypayPayment $payment)
    {
        $payment->load('order');

        if (empty($payment->refund_id)) {
            return redirect()->back()->with('error', 'No refund id available for this payment');
        }

        try {
            $svc = app(\App\Services\EasypayService::class);
            $resp = $svc->getRefund($payment->refund_id);
        } catch (\Throwable $e) {
            logger()->warning('Admin refund-refresh failed', ['payment_id' => $payment->id, 'err' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Failed to fetch refund details');
        }

        if (! is_array($resp) || empty($resp['body'])) {
            return redirect()->back()->with('error', 'No refund details available from Easypay');
        }

        // Merge refund body into the payment raw_response under `refund` key (preserve existing data)
        $existing = is_array($payment->raw_response) ? $payment->raw_response : [];
        $existing['refund'] = $resp['body'];
        $payment->raw_response = $existing;

        try {
            $payment->save();
        } catch (\Throwable $e) {
            logger()->warning('Admin refund-refresh: persist failed', ['payment_id' => $payment->id, 'err' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Failed to persist refund details');
        }

        $refundStatus = data_get($resp, 'body.status');
        $msg = 'Refund details refreshed';
        if ($refundStatus) {
            $msg .= " (status: {$refundStatus})";
        }

        return redirect()->back()->with('success', $msg);
    }
}
