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
        return response()->noContent();
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
