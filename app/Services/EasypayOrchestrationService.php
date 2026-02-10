<?php

namespace App\Services;

use App\Models\Order;
use App\Models\EasypayCheckoutSession;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

/**
 * Small orchestration helpers for Easypay-related pay-page flows.
 * Behaviour is intentionally thin and mirrors the controller logic so
 * refactors can be done incrementally while keeping the public surface
 * and behaviour unchanged.
 */
class EasypayOrchestrationService
{
    /**
     * Return true when the session is active, pending and younger than TTL (seconds).
     */
    public static function isSessionFresh(EasypayCheckoutSession $s, int $ttl): bool
    {
        if (! $s->is_active || $s->status !== 'pending' || ! $s->updated_at) {
            return false;
        }

        $age = now()->getTimestamp() - $s->updated_at->getTimestamp();
        return $age < $ttl;
    }

    /**
     * Return the latest active/pending manifest for the order if it's fresh according to TTL.
     */
    public function getLatestActiveManifest(Order $order, int $ttl): ?array
    {
        $latest = $order->easypayCheckoutSessions()->latest('updated_at')->first();
        if (! $latest) return null;

        if (self::isSessionFresh($latest, $ttl)) {
            return json_decode($latest->message ?? 'null', true);
        }

        return null;
    }

    /**
     * Ensure payload exists and attempt to create a checkout session (calls EasypayService).
     * Returns [ 'manifest' => ?array, 'message' => ?string ].
     * Behaviour matches the controller's orchestration (idempotent where possible).
     */
    public function ensurePayloadAndSession(Order $order): array
    {
        // Defensive: do not attempt anything when Easypay is disabled
        if (! config('easypay.enabled', false)) {
            return ['manifest' => null, 'message' => t('checkout.gateways.disabled') ?: (t('checkout.pay.unavailable') ?: 'Payment system is temporarily unavailable — please check your order details in a moment and try again.')];
        }

        // Ensure payload exists (service returns existing if present)
        $payload = $order->easypayPayload ?? EasypayService::createOrGetPayload($order);

        if (! $payload) {
            return [
                'manifest' => null,
                'message' => t('checkout.pay.unavailable') ?: 'Payment system is temporarily unavailable — please check your order details in a moment and try again.',
            ];
        }

        $newSession = EasypayService::createCheckoutSession($payload);

        if ($newSession->in_error || ! $newSession->is_active || $newSession->status !== 'pending') {
            $msg = t('checkout.pay.unavailable') ?: 'Payment system is temporarily unavailable — please check your order details in a moment and try again.';
            if (config('app.debug')) {
                $debug = is_string($newSession->message) ? $newSession->message : json_encode($newSession->message);
                $msg = ($msg . ' ' . (t('checkout.pay.unavailable_debug', ['error' => $debug]) ?: $debug));
            }

            return ['manifest' => null, 'message' => $msg];
        }

        $active = $order->easypayCheckoutSessions()
            ->where('is_active', true)
            ->where('status', 'pending')
            ->latest('updated_at')
            ->first();

        $manifest = $active ? json_decode($active->message ?? 'null', true) : null;

        return ['manifest' => $manifest, 'message' => ($manifest ? null : (t('checkout.pay.unavailable') ?: 'Payment system is temporarily unavailable — please check your order details in a moment and try again.'))];
    }

    /**
     * Build the user-facing unavailable message (append debug when available).
     */
    public static function buildPayUnavailableMessage(?string $debug = null): string
    {
        $msg = t('checkout.pay.unavailable') ?: 'Payment system is temporarily unavailable — please check your order details in a moment and try again.';
        if (config('app.debug') && $debug) {
            $msg = $msg . ' ' . (t('checkout.pay.unavailable_debug', ['error' => $debug]) ?: $debug);
        }

        return $msg;
    }

    /**
     * Prepare SDK for the pay page: perform server-side checks and when necessary
     * cancel pending sessions, refresh payment details and create a new session.
     * Returns an array with keys: action (ok|already-paid|new-manifest|error), message, manifest
     */
    public function prepareSdkForOrder(Order $order): array
    {
        // Defensive: do not run when Easypay disabled
        if (! config('easypay.enabled', false)) {
            return ['action' => 'error', 'message' => t('checkout.gateways.disabled') ?: (t('checkout.pay.unavailable') ?: 'Payment system is temporarily unavailable'), 'manifest' => null];
        }

        $latestSession = $order->easypayCheckoutSessions()->latest('updated_at')->first();
        $lastPayment = $order->easypayPayments()->latest('created_at')->first();

        if ($order->is_paid && $lastPayment && $lastPayment->payment_status === 'paid') {
            return ['action' => 'already-paid', 'message' => t('checkout.pay.already_paid') ?: 'Order already paid', 'manifest' => null];
        }

        if ($latestSession && $latestSession->is_active && $lastPayment && $lastPayment->payment_status === 'pending' && ! $order->is_paid) {
            $cancel = \App\Services\EasypayService::cancelCheckout($latestSession->checkout_id);
            $latestSession->update(['is_active' => false, 'status' => ($cancel['ok'] ? 'canceled' : 'error'), 'in_error' => $cancel['ok'] ? false : true]);

            if ($lastPayment->payment_id) {
                $single = (new \App\Services\EasypayService())->getSinglePayment($lastPayment->payment_id);
                if ($single) {
                    $lastPayment->update([
                        'payment_status' => data_get($single, 'payment_status') ?? data_get($single, 'payment.status'),
                        'paid_at' => data_get($single, 'paid_at') ? \Carbon\Carbon::parse(data_get($single, 'paid_at')) : null,
                        'raw_response' => $single,
                    ]);

                    // Only treat the remote payment as authoritative when it reports exactly 'paid'.
                    if (($lastPayment->payment_status ?? null) === 'paid') {
                        $order->markAsPaid('easypay', ['payment_id' => $lastPayment->payment_id]);
                        return ['action' => 'already-paid', 'message' => t('checkout.pay.already_paid') ?: 'Order already paid', 'manifest' => null];
                    }
                }
            }

            $payload = $order->easypayPayload ?? \App\Services\EasypayService::createOrGetPayload($order);
            $new = \App\Services\EasypayService::createCheckoutSession($payload);
            $manifest = $new->message ? json_decode($new->message, true) : null;

            if ($manifest && $new->is_active) {
                return ['action' => 'new-manifest', 'message' => t('checkout.pay.new_session_created') ?: 'New payment session created', 'manifest' => $manifest];
            }

            return ['action' => 'error', 'message' => self::buildPayUnavailableMessage($new->message ?? null), 'manifest' => null];
        }

        return ['action' => 'ok', 'message' => null, 'manifest' => null];
    }

    /**
     * Handle SDK-originated errors server-side and return an action for client.
     */
    public function handleSdkError(Order $order, array $error): array
    {
        $code = $error['code'] ?? null;
        $checkoutId = $error['checkoutId'] ?? $error['checkout_id'] ?? data_get($error, 'checkout.id');
        $paymentId = data_get($error, 'payment.id') ?? data_get($error, 'paymentId');

        if ($code === 'checkout-expired') {
            $payload = $order->easypayPayload ?? \App\Services\EasypayService::createOrGetPayload($order);
            $new = \App\Services\EasypayService::createCheckoutSession($payload);
            $manifest = $new->message ? json_decode($new->message, true) : null;
            return $manifest && $new->is_active ? ['action' => 'new-manifest', 'manifest' => $manifest] : ['action' => 'error', 'message' => self::buildPayUnavailableMessage($new->message ?? null)];
        }

        if ($code === 'already-paid') {
            $lastPayment = $order->easypayPayments()->latest('created_at')->first();
            if ($order->is_paid && $lastPayment && $lastPayment->payment_status === 'paid') {
                return ['action' => 'already-paid', 'message' => t('checkout.pay.already_paid')];
            }

            if ($paymentId) {
                $single = (new \App\Services\EasypayService())->getSinglePayment($paymentId);
                if ($single) {
                    // Build authoritative attrs from remote response
                    $attrs = [
                        'payment_id' => data_get($single, 'id') ?? $paymentId,
                        'checkout_id' => $checkoutId,
                        'order_id' => $order->id,
                        'payment_status' => data_get($single,'payment_status') ?? data_get($single,'payment.status'),
                        'paid_at' => data_get($single, 'paid_at') ? \Carbon\Carbon::parse(data_get($single, 'paid_at')) : null,
                        'raw_response' => $single,
                    ];

                    // Prefer an explicit order-scoped update (avoid accidental cross-order matches)
                    $existing = \App\Models\EasypayPayment::where('payment_id', $attrs['payment_id'])->where('order_id', $order->id)->first();

                    if ($existing) {
                        $existing->fill(array_filter($attrs, fn ($v) => ! is_null($v)));
                        $existing->save();
                    } else {
                        \App\Models\EasypayPayment::create($attrs);
                    }

                    // Ensure DB row(s) for this order+payment_id/checkout_id are updated (defensive, SQL-level)
                    \App\Models\EasypayPayment::where('order_id', $order->id)
                        ->where(function ($q) use ($attrs, $checkoutId) {
                            $q->where('payment_id', $attrs['payment_id']);
                            if (! empty($checkoutId)) {
                                $q->orWhere('checkout_id', $checkoutId);
                            }
                        })
                        ->update([
                            'payment_status' => $attrs['payment_status'] ?? null,
                            'paid_at' => $attrs['paid_at'] ?? null,
                            'raw_response' => $attrs['raw_response'] ?? null,
                        ]);

                    // Authoritative reload (scoped to order + payment_id)
                    $stored = \App\Models\EasypayPayment::where('payment_id', $attrs['payment_id'])->where('order_id', $order->id)->first();
                    $status = $stored ? $stored->payment_status : ($attrs['payment_status'] ?? null);



                    // Fallback: if order-scoped row wasn't updated but remote shows paid, try a global update by payment_id
                    $remoteStatus = $attrs['payment_status'] ?? null;
                    if (! in_array($status, ['paid','success'], true) && in_array($remoteStatus, ['paid','success'], true)) {
                        \App\Models\EasypayPayment::where('payment_id', $attrs['payment_id'])->update([
                            'payment_status' => $remoteStatus,
                            'paid_at' => $attrs['paid_at'],
                            'raw_response' => $attrs['raw_response'],
                        ]);

                        $stored = \App\Models\EasypayPayment::where('payment_id', $attrs['payment_id'])->where('order_id', $order->id)->first() ?? \App\Models\EasypayPayment::where('payment_id', $attrs['payment_id'])->first();
                        $status = $stored ? $stored->payment_status : $status;
                    }

                    // If remote indicates paid (authoritative), mark order paid. Do NOT treat other statuses as paid here.
                    if ($status === 'paid') {
                        $order->is_paid = true;
                        $order->status = 'PROCESSING';
                        $order->save();

                        if (app()->environment('testing')) {
                            logger()->info('easypay.test-log: orchestration-mark-order-paid', ['order_id' => $order->id, 'payment_id' => $attrs['payment_id'], 'status' => $status]);
                        }

                        if ($stored && $stored->payment_status !== $status) {
                            $stored->payment_status = $status;
                            $stored->paid_at = $stored->paid_at ?? $attrs['paid_at'];
                            $stored->raw_response = $single;
                            $stored->save();

                            if (app()->environment('testing')) {
                                logger()->info('easypay.test-log: orchestration-updated-payment-row', ['payment_id' => $stored->payment_id, 'order_id' => $stored->order_id, 'payment_status' => $stored->payment_status]);
                            }
                        }

                        return ['action' => 'already-paid', 'message' => t('checkout.pay.already_paid')];
                    }
                }
            }

            if ($checkoutId) {
                \App\Services\EasypayService::cancelCheckout($checkoutId);
                \App\Models\EasypayCheckoutSession::where('checkout_id', $checkoutId)->update(['is_active' => false, 'status' => 'canceled']);
            }

            $payload = $order->easypayPayload ?? \App\Services\EasypayService::createOrGetPayload($order);
            $new = \App\Services\EasypayService::createCheckoutSession($payload);
            $manifest = $new->message ? json_decode($new->message, true) : null;

            return $manifest && $new->is_active ? ['action' => 'new-manifest', 'manifest' => $manifest] : ['action' => 'error', 'message' => self::buildPayUnavailableMessage($new->message ?? null)];
        }

        if (in_array($code, ['generic-error','payment-failure'], true)) {
            if ($paymentId) {
                $single = (new \App\Services\EasypayService())->getSinglePayment($paymentId);
                if ($single) {
                    $attrs = [
                        'checkout_id' => $checkoutId,
                        'order_id' => $order->id,
                        'payment_status' => data_get($single,'payment_status') ?? data_get($single,'payment.status'),
                        'raw_response' => $single,
                    ];

                    \App\Models\EasypayPayment::updateOrCreate(['payment_id' => data_get($single,'id')], $attrs);

                    $stored = \App\Models\EasypayPayment::where('payment_id', data_get($single,'id'))->first();
                    $status = $stored ? $stored->payment_status : ($attrs['payment_status'] ?? null);

                    // Require authoritative remote confirmation (exactly 'paid').
                    if ($status === 'paid') {
                        $order->markAsPaid('easypay', ['payment_id' => data_get($single,'id')]);

                        // ensure stored payment reflects paid status (persist paid_at/raw_response if needed)
                        if ($stored && $stored->payment_status !== $status) {
                            $stored->payment_status = $status;
                            $stored->paid_at = data_get($single, 'paid_at') ? \Carbon\Carbon::parse(data_get($single, 'paid_at')) : $stored->paid_at;
                            $stored->raw_response = $single;
                            $stored->save();
                        }

                        return ['action' => 'already-paid', 'message' => t('checkout.pay.already_paid')];
                    }
                }
            }

            if ($checkoutId) {
                \App\Services\EasypayService::cancelCheckout($checkoutId);
                \App\Models\EasypayCheckoutSession::where('checkout_id', $checkoutId)->update(['is_active' => false, 'status' => 'canceled']);
            }

            $payload = $order->easypayPayload ?? \App\Services\EasypayService::createOrGetPayload($order);
            $new = \App\Services\EasypayService::createCheckoutSession($payload);
            $manifest = $new->message ? json_decode($new->message, true) : null;

            return $manifest && $new->is_active ? ['action' => 'new-manifest', 'manifest' => $manifest] : ['action' => 'error', 'message' => self::buildPayUnavailableMessage($new->message ?? null)];
        }

        return ['action' => 'error', 'message' => t('checkout.pay.unavailable')];
    }
}
