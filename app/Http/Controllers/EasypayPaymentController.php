<?php

namespace App\Http\Controllers;

use App\Models\EasypayCheckoutSession;
use App\Models\EasypayPayment;
use App\Models\Order;
use App\Services\EasypayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EasypayPaymentController extends Controller
{
    protected EasypayService $service;

    public function __construct(EasypayService $service)
    {
        $this->service = $service;
        $this->middleware('auth');
    }

    public function logSdkError(Request $request)
    {
        logger()->warning('Easypay SDK error from client', $request->all());

        $payload = $request->json()->all() ?? [];
        $error = $payload['error'] ?? $payload;
        $checkoutId = data_get($error, 'checkoutId') ?? data_get($error, 'checkout_id');
        $order = null;

        if ($checkoutId) {
            $session = EasypayCheckoutSession::where('checkout_id', $checkoutId)->first();
            if ($session && $session->order_id) {
                $order = Order::find($session->order_id);
            }
        }

        // If SDK provided order uuid or route provides it, try to resolve
        if (! $order && $request->route('order') instanceof Order) {
            $order = $request->route('order');
        }

        if (! $order) {
            return response()->noContent();
        }

        // Defensive: if Easypay is disabled do nothing
        if (! config('easypay.enabled', false)) {
            return response()->json(['action' => 'error', 'message' => t('checkout.gateways.disabled') ?: (t('checkout.pay.unavailable') ?: 'Payment system is temporarily unavailable')]);
        }

        $orch = new \App\Services\EasypayOrchestrationService;
        $result = $orch->handleSdkError($order, (array) $error);

        // extract paymentId from the SDK payload (controller-level)
        $paymentId = data_get($error, 'payment.id') ?? data_get($error, 'paymentId');

        // Controller-level authoritative fallback: if orchestration reports already-paid but
        // persistence wasn't visible (suite-order flakes), fetch remote and force-update DB
        if (($result['action'] ?? null) === 'already-paid' && ! empty($paymentId)) {
            $single = $this->service->getSinglePayment($paymentId);
            if ($single) {
                $status = data_get($single, 'payment_status') ?? data_get($single, 'payment.status');
                // Only treat 'paid' from the authoritative endpoint as confirmation.
                if ($status === 'paid') {
                    \App\Models\EasypayPayment::where('payment_id', data_get($single, 'id'))->update([
                        'payment_status' => $status,
                        'paid_at' => data_get($single, 'paid_at') ? \Carbon\Carbon::parse(data_get($single, 'paid_at')) : null,
                        'raw_response' => $single,
                    ]);

                    if ($order) {
                        $order->markAsPaid('easypay', ['payment_id' => data_get($single, 'id')]);

                        if (app()->environment('testing')) {
                            logger()->info('easypay.test-log: controller-mark-order-paid', ['order_id' => $order->id, 'payment_id' => data_get($single, 'id')]);
                        }
                    } else {
                        // fallback: find order via the persisted EasypayPayment (defensive, ensure DB authoritative)
                        $pr = \App\Models\EasypayPayment::where('payment_id', data_get($single, 'id'))->first();
                        if ($pr && $pr->order_id && $pr->payment_status === 'paid') {
                            $o = \App\Models\Order::find($pr->order_id);
                            if ($o) {
                                $o->markAsPaid('easypay', ['payment_id' => $pr->payment_id]);

                                if (app()->environment('testing')) {
                                    logger()->info('easypay.test-log: controller-mark-order-paid-via-payment', ['order_id' => $o->id, 'payment_id' => $pr->payment_id]);
                                }
                            }
                        }
                    }
                }
            }
        }

        // Return JSON so client can take the appropriate action (restart SDK, show message, etc.)
        return response()->json($result);
    }

    /**
     * Prepare the SDK before the client attempts to start it. This performs the
     * checks described in the spec (already-paid, pending payments -> cancel+recreate, etc.)
     */
    public function prepareSdk(Request $request, Order $order)
    {
        $orch = new \App\Services\EasypayOrchestrationService;
        $result = $orch->prepareSdkForOrder($order);

        return response()->json($result);
    }

    /**
     * Receive checkoutInfo from client SDK onSuccess and persist payment details.
     */
    public function store(Request $request)
    {
        $data = $request->json()->all();

        // Accept multiple possible shapes: { id: <checkoutId>, payment: { id: <paymentId>, status: ... } }
        // Support payload wrapped as { checkout: { ... } } as used by the pay page
        $wrapper = data_get($data, 'checkout') ?? $data;

        $checkoutId = data_get($wrapper, 'id') ?? data_get($wrapper, 'checkoutId') ?? data_get($wrapper, 'checkout_id');
        $paymentId = data_get($wrapper, 'payment.id') ?? data_get($wrapper, 'paymentId') ?? data_get($wrapper, 'payment_id');

        if (empty($paymentId)) {
            return response()->json(['error' => 'payment id missing'], 422);
        }

        // Determine order_id: try to find by checkout session, route param, or accept order_uuid from payload
        $orderId = null;
        // If route provides order (route-model binding), prefer it
        $routeOrder = $request->route('order');
        if ($routeOrder instanceof \App\Models\Order) {
            $orderId = $routeOrder->id;
        }

        if (! empty($checkoutId)) {
            $session = EasypayCheckoutSession::where('checkout_id', $checkoutId)->first();
            if ($session && $session->order_id) {
                $orderId = $session->order_id;
            }
        }

        if (! $orderId && $request->has('order_uuid')) {
            $order = Order::where('uuid', $request->input('order_uuid'))->first();
            if ($order) {
                $orderId = $order->id;
            }
        }

        // Persist the SDK-provided checkoutInfo immediately — the SDK always supplies a payment id.
        // We treat the client payload as the source-of-truth for initial persistence so retries succeed
        // even when the Easypay API is temporarily unreachable. The authoritative Easypay single-payment
        // will be fetched afterwards (best-effort) and used to update the persisted row and mark the
        // order as paid only when remote status === 'paid'.
        $clientStatus = data_get($wrapper, 'payment.status') ?? data_get($wrapper, 'payment_status') ?? null;
        $clientPaidAt = data_get($wrapper, 'payment.paid_at') ?? data_get($wrapper, 'paid_at') ?? null;

        // Upsert by payment_id to avoid duplicates on SDK retries
        $record = EasypayPayment::firstOrNew(['payment_id' => data_get($wrapper, 'payment.id') ?? data_get($wrapper, 'id') ?? $paymentId]);
        $record->checkout_id = $checkoutId;
        $record->order_id = $orderId;
        $record->payment_status = $clientStatus ?? ($record->payment_status ?? 'pending');
        $record->paid_at = $clientPaidAt ? \Carbon\Carbon::parse($clientPaidAt) : ($record->paid_at ?? null);
        $record->payment_method = data_get($wrapper, 'payment.method') ?? data_get($wrapper, 'method.type') ?? $record->payment_method;
        $record->card_type = data_get($wrapper, 'payment.card_type') ?? data_get($wrapper, 'method.card_type') ?? $record->card_type;
        $record->card_last_digits = data_get($wrapper, 'payment.lastFour') ?? data_get($wrapper, 'method.last_four') ?? $record->card_last_digits;
        $record->mb_entity = data_get($wrapper, 'payment.entity') ?? data_get($wrapper, 'method.entity') ?? $record->mb_entity;
        $record->mb_reference = data_get($wrapper, 'payment.reference') ?? data_get($wrapper, 'method.reference') ?? $record->mb_reference;
        $record->mb_expiration_time = data_get($wrapper, 'payment.multibanco.expiration_time') ? \Carbon\Carbon::parse(data_get($wrapper, 'payment.multibanco.expiration_time')) : ($record->mb_expiration_time ?? null);
        $record->iban = data_get($wrapper, 'payment.iban') ?? data_get($wrapper, 'method.sdd_mandate.iban') ?? $record->iban;
        $record->raw_response = $wrapper;

        try {
            $record->save();
        } catch (\Exception $e) {
            Log::error('EasypayPayment store (client-persist) error: '.$e->getMessage(), ['payload' => $wrapper]);

            return response()->json(['error' => 'could not persist payment'], 500);
        }

        // Best-effort authoritative refresh: update persisted row if remote is reachable
        try {
            $single = $this->service->getSinglePayment($paymentId);
        } catch (\Throwable $e) {
            $single = null;
            Log::warning('EasypayPaymentController: remote single payment fetch failed', ['payment_id' => $paymentId, 'error' => $e->getMessage()]);
        }

        if (! empty($single) && is_array($single)) {
            $attrs = [
                'payment_status' => data_get($single, 'payment_status') ?? data_get($single, 'payment.status') ?? $record->payment_status,
                'paid_at' => data_get($single, 'paid_at') ? \Carbon\Carbon::parse(data_get($single, 'paid_at')) : $record->paid_at,
                'mb_entity' => data_get($single, 'method.entity') ?? data_get($single, 'payment.entity') ?? $record->mb_entity,
                'mb_reference' => data_get($single, 'method.reference') ?? data_get($single, 'payment.reference') ?? $record->mb_reference,
                'mb_expiration_time' => data_get($single, 'multibanco.expiration_time') ? \Carbon\Carbon::parse(data_get($single, 'multibanco.expiration_time')) : $record->mb_expiration_time,
                'iban' => data_get($single, 'method.sdd_mandate.iban') ?? data_get($single, 'method.sdd_mandate') ?? $record->iban,
                'raw_response' => $single,
            ];

            try {
                $record->update($attrs);
                $record->refresh();
            } catch (\Exception $e) {
                Log::warning('EasypayPaymentController: could not update persisted payment with remote response', ['payment_id' => $paymentId, 'err' => $e->getMessage()]);
            }

            $remoteStatus = data_get($single, 'payment_status') ?? data_get($single, 'payment.status');
            if ($orderId && $remoteStatus === 'paid') {
                $order = Order::find($orderId);
                if ($order) {
                    $order->markAsPaid('easypay', ['payment_id' => $paymentId]);
                }
            }
        }

        // Contract: onSuccess must persist. Return the persisted row, whether we obtained authoritative data,
        // and a user-facing message when appropriate so the client SDK can show immediate feedback.
        $finalStatus = $remoteStatus ?? $clientStatus ?? $record->payment_status;

        $message = null;
        switch ($finalStatus) {
            case 'paid':
                // prefer the immediate "payment received" message for SDK success flows
                $message = t('checkout.pay.success') ?: 'Payment received — thank you. Updating order status…';
                break;

            case 'authorised':
                $message = t('checkout.pay.status.authorised') ?: 'Payment authorised — processing is underway, please check your order details in a moment.';
                break;

            case 'pending':
                // payment info was created (MB/IBAN/etc.) — instruct the user to follow the payment method steps
                $message = t('checkout.pay.on_success.pending') ?: 'Payment info created. Please follow the instructions to complete the payment and your order will be processed afterwards.';
                break;

            default:
                $message = null;
        }

        // Only persist the flash to session when the server response is authoritative
        // and represents a final 'paid' state (the client will navigate to the order page).
        // For non-authoritative or informational messages show inline only (do not persist)
        // — this prevents duplicate flashes when the client displays the message immediately.
        $flashType = null;
        if (! empty($message)) {
            $flashType = match ($finalStatus) {
                'paid' => 'success',
                'authorised' => 'info',
                'pending' => 'warning',
                default => 'info',
            };

            $shouldPersistToSession = (! empty($single) && is_array($single) && $finalStatus === 'paid');

            if ($shouldPersistToSession) {
                // Persist only for authoritative paid responses so a following redirect
                // (client-initiated) will show the server flash exactly once.
                session()->flash('success', $message);
                session()->flash('flash_type', $flashType);
            }
        }

        return response()->json([
            'ok' => true,
            'payment' => $record,
            'paymentStatus' => $finalStatus,
            'authoritative' => (bool) (! empty($single) && is_array($single)),
            'message' => $message,
            'type' => $flashType ?? null,
        ], 201);
    }
}
