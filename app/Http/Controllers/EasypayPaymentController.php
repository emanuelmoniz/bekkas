<?php

namespace App\Http\Controllers;

use App\Models\EasypayPayment;
use App\Models\EasypayCheckoutSession;
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

        $orch = new \App\Services\EasypayOrchestrationService();
        $result = $orch->handleSdkError($order, (array) $error);

        // extract paymentId from the SDK payload (controller-level)
        $paymentId = data_get($error, 'payment.id') ?? data_get($error, 'paymentId');

        // Controller-level authoritative fallback: if orchestration reports already-paid but
        // persistence wasn't visible (suite-order flakes), fetch remote and force-update DB
        if (($result['action'] ?? null) === 'already-paid' && ! empty($paymentId)) {
            $single = $this->service->getSinglePayment($paymentId);
            if ($single) {
                $status = data_get($single, 'payment_status') ?? data_get($single, 'payment.status');
                if (in_array($status, ['paid','success'], true)) {
                    \App\Models\EasypayPayment::where('payment_id', data_get($single,'id'))->update([
                        'payment_status' => $status,
                        'paid_at' => data_get($single, 'paid_at') ? \Carbon\Carbon::parse(data_get($single, 'paid_at')) : null,
                        'raw_response' => $single,
                    ]);

                    if ($order) {
                        $order->is_paid = true;
                        $order->status = 'PROCESSING';
                        $order->save();

                        if (app()->environment('testing')) {
                            logger()->info('easypay.test-log: controller-mark-order-paid', ['order_id' => $order->id, 'payment_id' => data_get($single,'id')]);
                        }
                    } else {
                        // fallback: find order via the persisted EasypayPayment (defensive, ensure DB authoritative)
                        $pr = \App\Models\EasypayPayment::where('payment_id', data_get($single,'id'))->first();
                        if ($pr && $pr->order_id) {
                            $o = \App\Models\Order::find($pr->order_id);
                            if ($o) { $o->is_paid = true; $o->status = 'PROCESSING'; $o->save();
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
        $orch = new \App\Services\EasypayOrchestrationService();
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

        if (!empty($checkoutId)) {
            $session = EasypayCheckoutSession::where('checkout_id', $checkoutId)->first();
            if ($session && $session->order_id) {
                $orderId = $session->order_id;
            }
        }

        if (!$orderId && $request->has('order_uuid')) {
            $order = Order::where('uuid', $request->input('order_uuid'))->first();
            if ($order) $orderId = $order->id;
        }

        // Call Easypay API for single payment details
        $single = $this->service->getSinglePayment($paymentId);

        if (!$single) {
            return response()->json(['error' => 'could not fetch payment details from Easypay'], 502);
        }

        try {
            $record = EasypayPayment::create([
                'payment_id' => data_get($single, 'id', $paymentId),
                'checkout_id' => $checkoutId,
                'order_id' => $orderId,
                'payment_status' => data_get($single, 'payment_status') ?? data_get($single, 'payment.status') ?? data_get($data, 'payment.status'),
                'paid_at' => data_get($single, 'paid_at') ? \Carbon\Carbon::parse(data_get($single, 'paid_at')) : null,
                'payment_method' => data_get($single, 'method.type') ?? data_get($single, 'payment.method') ?? data_get($data, 'payment.method'),
                'card_type' => data_get($single, 'method.card_type') ?? data_get($single, 'payment.cardType') ?? null,
                'card_last_digits' => data_get($single, 'method.last_four') ?? data_get($single, 'payment.lastFour') ?? null,
                'mb_entity' => data_get($single, 'method.entity') ?? data_get($single, 'payment.entity') ?? null,
                'mb_reference' => data_get($single, 'method.reference') ?? data_get($single, 'payment.reference') ?? null,
                'mb_expiration_time' => data_get($single, 'multibanco.expiration_time') ? \Carbon\Carbon::parse(data_get($single, 'multibanco.expiration_time')) : null,
                'iban' => data_get($single, 'method.sdd_mandate.iban') ?? data_get($single, 'method.sdd_mandate') ?? null,
                'raw_response' => $single,
            ]);
        } catch (\Exception $e) {
            Log::error('EasypayPayment store error: ' . $e->getMessage(), ['payload' => $single]);
            return response()->json(['error' => 'could not persist payment'], 500);
        }

        // If paid, mark order as paid and set status to PROCESSING
        if ($orderId && (data_get($single, 'payment_status') === 'paid' || data_get($single, 'payment_status') === 'success' || data_get($single, 'payment.status') === 'paid')) {
            $order = Order::find($orderId);
            if ($order) {
                $order->is_paid = true;
                $order->status = 'PROCESSING';
                $order->save();
            }
        }

        return response()->json(['ok' => true, 'payment' => $record], 201);
    }
}
