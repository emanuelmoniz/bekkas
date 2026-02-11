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

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
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
}
