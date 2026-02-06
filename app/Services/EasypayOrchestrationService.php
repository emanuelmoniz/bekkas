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
}
