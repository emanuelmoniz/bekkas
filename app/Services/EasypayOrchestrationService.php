<?php

namespace App\Services;

use App\Models\EasypayCheckoutSession;
use App\Models\Order;

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
        if (! $latest) {
            return null;
        }

        if (self::isSessionFresh($latest, $ttl)) {
            return $latest->manifest; // authoritative manifest built from DB fields
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
                $msg = ($msg.' '.(t('checkout.pay.unavailable_debug', ['error' => $debug]) ?: $debug));
            }

            return ['manifest' => null, 'message' => $msg];
        }

        $active = $order->easypayCheckoutSessions()
            ->where('is_active', true)
            ->where('status', 'pending')
            ->latest('updated_at')
            ->first();

        $manifest = $active ? $active->manifest : null;

        return ['manifest' => $manifest, 'message' => ($manifest ? null : (t('checkout.pay.unavailable') ?: 'Payment system is temporarily unavailable — please check your order details in a moment and try again.'))];
    }

    /**
     * Preflight cleanup run on the pay page before any orchestration/SDK start.
     * - cancel sessions older than TTL
     * - when multiple active sessions inside TTL: cancel all but the most recent
     * - fetch authoritative checkout details for persisted sessions and update DB
     * - fetch single-payment details for cancelled sessions when applicable
     */
    public function preflightCleanup(Order $order, int $ttl): void
    {
        $now = now();
        $sessions = $order->easypayCheckoutSessions()->get();

        // NEW ORDER (per request):
        // A) Fetch authoritative checkout details for *every* persisted session first.
        foreach ($sessions as $s) {
            try {
                $f = EasypayService::fetchCheckout($s->checkout_id);
                if (! empty($f) && array_key_exists('body', $f) && ! empty($f['body'])) {
                    $body = is_array($f['body']) ? $f['body'] : json_decode((string) $f['body'], true);
                    $s->message = json_encode($body);
                    $s->checkout_id = data_get($body, 'checkout.id') ?? $s->checkout_id;
                    $s->session_id = data_get($body, 'checkout.session') ?? data_get($body, 'session') ?? $s->session_id;

                    $remoteStatus = data_get($body, 'checkout.status') ?? data_get($body, 'status');
                    if (! empty($remoteStatus) && is_string($remoteStatus)) {
                        // only apply remote status when the session is still in a neutral/active state
                        if (empty($s->status) || in_array($s->status, ['pending', 'created'], true)) {
                            $s->status = $remoteStatus;
                            $s->is_active = in_array($remoteStatus, ['pending', 'created'], true);
                            $s->in_error = $remoteStatus === 'failed';
                        }
                    }

                    if (! empty($f['status']) && $f['status'] >= 400) {
                        $s->error_code = $f['status'];
                    }

                    $s->save();
                }
            } catch (\Throwable $e) {
                logger()->warning('EasypayOrchestration (preflight): initial fetchCheckout failed', ['checkout_id' => $s->checkout_id, 'err' => $e->getMessage()]);
            }
        }

        // B) Cancel sessions that are active but older than TTL
        foreach ($order->easypayCheckoutSessions()->get() as $s) {
            $age = $s->updated_at ? ($now->getTimestamp() - $s->updated_at->getTimestamp()) : PHP_INT_MAX;
            if ($s->is_active && $age >= $ttl) {
                try {
                    $res = EasypayService::cancelCheckout($s->checkout_id);
                    $s->update(['is_active' => false, 'status' => ($res['ok'] ? 'canceled' : 'error'), 'in_error' => $res['ok'] ? false : true]);
                } catch (\Throwable $e) {
                    $s->update(['is_active' => false, 'status' => 'error', 'in_error' => true]);
                    logger()->warning('EasypayOrchestration (preflight): failed to cancel expired checkout', ['checkout_id' => $s->checkout_id, 'err' => $e->getMessage()]);
                }
            }
        }

        // C) Deduplicate active sessions inside TTL: keep only the most-recent active one
        $activeFresh = $order->easypayCheckoutSessions()->where('is_active', true)->get()->filter(fn($x) => self::isSessionFresh($x, $ttl));
        if ($activeFresh->count() > 1) {
            $keep = $activeFresh->sortByDesc('updated_at')->first();
            $toCancel = $activeFresh->reject(fn($x) => $x->id === $keep->id);
            foreach ($toCancel as $s) {
                try {
                    $res = EasypayService::cancelCheckout($s->checkout_id);
                    $s->update(['is_active' => false, 'status' => ($res['ok'] ? 'canceled' : 'error'), 'in_error' => $res['ok'] ? false : true]);
                } catch (\Throwable $e) {
                    $s->update(['is_active' => false, 'status' => 'error', 'in_error' => true]);
                    logger()->warning('EasypayOrchestration (preflight): failed to cancel duplicate active session', ['checkout_id' => $s->checkout_id, 'err' => $e->getMessage()]);
                }
            }
        }

        // D) For sessions that were canceled above, fetch authoritative checkout details again
        foreach ($order->easypayCheckoutSessions()->where('is_active', false)->get() as $s) {
            try {
                $f = EasypayService::fetchCheckout($s->checkout_id);
                if (! empty($f) && array_key_exists('body', $f) && ! empty($f['body'])) {
                    $body = is_array($f['body']) ? $f['body'] : json_decode((string) $f['body'], true);
                    $s->message = json_encode($body);
                    $s->checkout_id = data_get($body, 'checkout.id') ?? $s->checkout_id;
                    $s->session_id = data_get($body, 'checkout.session') ?? data_get($body, 'session') ?? $s->session_id;

                    $remoteStatus = data_get($body, 'checkout.status') ?? data_get($body, 'status');
                    if (! empty($remoteStatus) && is_string($remoteStatus)) {
                        // do not overwrite authoritative local cancel/error state
                        if (empty($s->status) || in_array($s->status, ['pending', 'created'], true)) {
                            $s->status = $remoteStatus;
                            $s->is_active = in_array($remoteStatus, ['pending', 'created'], true);
                            $s->in_error = $remoteStatus === 'failed';
                        }
                    }

                    if (! empty($f['status']) && $f['status'] >= 400) {
                        $s->error_code = $f['status'];
                    }

                    $s->save();
                }
            } catch (\Throwable $e) {
                logger()->warning('EasypayOrchestration (preflight): failed to fetch checkout for cancelled session', ['checkout_id' => $s->checkout_id, 'err' => $e->getMessage()]);
            }
        }

        // E) Refresh all payments for the order (best-effort)
        try {
            $payments = $order->easypayPayments()->get();
            foreach ($payments as $p) {
                // Skip already-cancelled payments — once we've marked them cancelled locally
                // we should not re-query and overwrite that authoritative state.
                if (($p->payment_status ?? null) === 'canceled') {
                    continue;
                }

                if (empty($p->payment_id)) {
                    continue;
                }

                try {
                    $single = (new \App\Services\EasypayService)->getSinglePayment($p->payment_id);
                } catch (\Throwable $e) {
                    logger()->warning('EasypayOrchestration (preflight): getSinglePayment failed', ['payment_id' => $p->payment_id, 'err' => $e->getMessage()]);
                    $single = null;
                }

                if (is_array($single) && ! empty($single)) {
                    $attrs = [
                        'payment_status' => data_get($single, 'payment_status') ?? data_get($single, 'payment.status') ?? $p->payment_status,
                        'paid_at' => data_get($single, 'paid_at') ? \Carbon\Carbon::parse(data_get($single, 'paid_at')) : $p->paid_at,
                        'mb_entity' => data_get($single, 'method.entity') ?? data_get($single, 'payment.entity') ?? $p->mb_entity,
                        'mb_reference' => data_get($single, 'method.reference') ?? data_get($single, 'payment.reference') ?? $p->mb_reference,
                        'mb_expiration_time' => data_get($single, 'multibanco.expiration_time') ? \Carbon\Carbon::parse(data_get($single, 'multibanco.expiration_time')) : $p->mb_expiration_time,
                        'iban' => data_get($single, 'method.sdd_mandate.iban') ?? data_get($single, 'method.sdd_mandate') ?? $p->iban,
                        'raw_response' => $single,
                    ];

                    try {
                        $p->update(array_filter($attrs, fn ($v) => ! is_null($v)));
                    } catch (\Throwable $e) {
                        logger()->warning('EasypayOrchestration (preflight): failed to persist refreshed payment', ['payment_id' => $p->payment_id, 'err' => $e->getMessage()]);
                    }
                }

                // F) If payment remains in `pending` state after refresh, attempt to DELETE it remotely
                if (($p->fresh()->payment_status ?? null) === 'pending') {
                    try {
                        $del = (new \App\Services\EasypayService)->deleteSinglePayment($p->payment_id);
                        if (! empty($del['ok']) && $del['status'] === 204) {
                            $p->update(['payment_status' => 'canceled', 'raw_response' => array_merge($p->raw_response ?? [], ['deleted' => true])]);
                        } else {
                            logger()->info('EasypayOrchestration (preflight): deleteSinglePayment returned non-204', ['payment_id' => $p->payment_id, 'res' => $del]);
                        }
                    } catch (\Throwable $e) {
                        logger()->warning('EasypayOrchestration (preflight): failed to delete pending payment', ['payment_id' => $p->payment_id, 'err' => $e->getMessage()]);
                    }
                }
            }
        } catch (\Throwable $e) {
            logger()->warning('EasypayOrchestration (preflight): payment refresh pass failed', ['order_id' => $order->id, 'err' => $e->getMessage()]);
        }
    }

    /**
     * Decide which manifest (if any) should be provided to the client to start the
     * SDK according to the product rules supplied in the ticket.
     * Returns [ 'manifest' => ?array, 'message' => ?string ]
     */
    public function getManifestForSdk(Order $order, int $ttl): array
    {
        // Preflight: cancel/refresh/fetch as requested
        $this->preflightCleanup($order, $ttl);

        // 1) No sessions at all OR all sessions inactive -> create one and return its manifest
        $sessions = $order->easypayCheckoutSessions()->latest('updated_at')->get();
        $anyActive = $sessions->contains(fn($x) => $x->is_active && $x->status === 'pending');
        if ($sessions->isEmpty() || ! $anyActive) {
            $payload = $order->easypayPayload ?? EasypayService::createOrGetPayload($order);
            $new = EasypayService::createCheckoutSession($payload);
            $manifest = $new->manifest;

            return $manifest && $new->is_active ? ['manifest' => $manifest, 'message' => null] : ['manifest' => null, 'message' => self::buildPayUnavailableMessage($new->message ?? null)];
        }

        // 2) There is at least one active session inside TTL — pick the most recent
        $fresh = $sessions->filter(fn($x) => $x->is_active && $x->status === 'pending' && self::isSessionFresh($x, $ttl))->sortByDesc('updated_at');
        $latest = $fresh->first();
        if ($latest) {
            // If there's NO corresponding payment record -> reuse this session
            $hasPayment = $order->easypayPayments()->where('checkout_id', $latest->checkout_id)->exists();
            if (! $hasPayment) {
                return ['manifest' => $latest->manifest, 'message' => null];
            }

            // If there IS a corresponding payment record, cancel/re-fetch/update/create-new and return new manifest
            try {
                $res = EasypayService::cancelCheckout($latest->checkout_id);
                $latest->update(['is_active' => false, 'status' => ($res['ok'] ? 'canceled' : 'error'), 'in_error' => $res['ok'] ? false : true]);
            } catch (\Throwable $e) {
                $latest->update(['is_active' => false, 'status' => 'error', 'in_error' => true]);
                logger()->warning('EasypayOrchestration (getManifestForSdk): failed to cancel session with existing payment', ['checkout_id' => $latest->checkout_id, 'err' => $e->getMessage()]);
            }

            // Persist authoritative checkout + payment details (best-effort)
            try {
                $f = EasypayService::fetchCheckout($latest->checkout_id);
                if (! empty($f) && array_key_exists('body', $f) && ! empty($f['body'])) {
                    $body = is_array($f['body']) ? $f['body'] : json_decode((string) $f['body'], true);
                    $latest->message = json_encode($body);
                    $latest->checkout_id = data_get($body, 'checkout.id') ?? $latest->checkout_id;
                    $latest->session_id = data_get($body, 'checkout.session') ?? data_get($body, 'session') ?? $latest->session_id;
                    $latest->save();
                }
            } catch (\Throwable $e) {
                logger()->warning('EasypayOrchestration (getManifestForSdk): failed to fetch checkout after cancel', ['checkout_id' => $latest->checkout_id, 'err' => $e->getMessage()]);
            }

            try {
                $payments = $order->easypayPayments()->where('checkout_id', $latest->checkout_id)->get();
                foreach ($payments as $p) {
                    // Do not re-query payments we've explicitly marked as cancelled locally.
                    if (($p->payment_status ?? null) === 'canceled') {
                        continue;
                    }

                    if (empty($p->payment_id)) continue;

                    $single = (new EasypayService)->getSinglePayment($p->payment_id);
                    if (is_array($single) && ! empty($single)) {
                        $p->update(array_filter([
                            'payment_status' => data_get($single, 'payment_status') ?? data_get($single, 'payment.status') ?? $p->payment_status,
                            'paid_at' => data_get($single, 'paid_at') ? \Carbon\Carbon::parse(data_get($single, 'paid_at')) : $p->paid_at,
                            'raw_response' => $single,
                        ], fn($v) => ! is_null($v)));
                    }
                }
            } catch (\Throwable $e) {
                logger()->warning('EasypayOrchestration (getManifestForSdk): failed to refresh payments after cancelling session', ['checkout_id' => $latest->checkout_id, 'err' => $e->getMessage()]);
            }

            // Create a fresh session and return its manifest so the client starts SDK with the new session
            $payload = $order->easypayPayload ?? EasypayService::createOrGetPayload($order);
            $new = EasypayService::createCheckoutSession($payload);
            $manifest = $new->manifest;

            return $manifest && $new->is_active ? ['manifest' => $manifest, 'message' => null] : ['manifest' => null, 'message' => self::buildPayUnavailableMessage($new->message ?? null)];
        }

        // If we reached here there are active sessions but none are fresh — create a new one as a fallback
        $payload = $order->easypayPayload ?? EasypayService::createOrGetPayload($order);
        $new = EasypayService::createCheckoutSession($payload);
        $manifest = $new->manifest;

        return $manifest && $new->is_active ? ['manifest' => $manifest, 'message' => null] : ['manifest' => null, 'message' => self::buildPayUnavailableMessage($new->message ?? null)];
    }
    /**
     * Build the user-facing unavailable message (append debug when available).
     */
    public static function buildPayUnavailableMessage(?string $debug = null): string
    {
        $msg = t('checkout.pay.unavailable') ?: 'Payment system is temporarily unavailable — please check your order details in a moment and try again.';
        if (config('app.debug') && $debug) {
            $msg = $msg.' '.(t('checkout.pay.unavailable_debug', ['error' => $debug]) ?: $debug);
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

            // Persist checkout details for the cancelled session (best-effort) before fetching payments
            try {
                $f = \App\Services\EasypayService::fetchCheckout($latestSession->checkout_id);
                if (! empty($f) && array_key_exists('body', $f) && ! empty($f['body'])) {
                    $body = is_array($f['body']) ? $f['body'] : json_decode((string) $f['body'], true);

                    $latestSession->message = json_encode($body);

                    $remoteStatus = data_get($body, 'checkout.status') ?? data_get($body, 'status');
                    if (! empty($remoteStatus) && is_string($remoteStatus)) {
                        // Do not overwrite local authoritative cancel/error state
                        if (empty($latestSession->status) || in_array($latestSession->status, ['pending', 'created'], true)) {
                            $latestSession->status = $remoteStatus;
                            $latestSession->is_active = in_array($remoteStatus, ['pending', 'created'], true);
                            $latestSession->in_error = $remoteStatus === 'failed';
                        } else {
                            logger()->debug('EasypayOrchestration (prepareSdk): preserving local session.status over remote', ['checkout_id' => $latestSession->checkout_id, 'local_status' => $latestSession->status, 'remote_status' => $remoteStatus]);
                        }
                    }

                    $latestSession->checkout_id = data_get($body, 'checkout.id') ?? $latestSession->checkout_id;
                    $latestSession->session_id = data_get($body, 'checkout.session') ?? data_get($body, 'session') ?? $latestSession->session_id;

                    if (! empty($f['status']) && $f['status'] >= 400) {
                        $latestSession->error_code = $f['status'];
                    }

                    $latestSession->save();
                }
            } catch (\Throwable $e) {
                logger()->warning('EasypayOrchestration: failed to fetch checkout after cancel (prepareSdk)', ['checkout_id' => $latestSession->checkout_id, 'err' => $e->getMessage()]);
            }

            if ($lastPayment->payment_id) {
                $single = (new \App\Services\EasypayService)->getSinglePayment($lastPayment->payment_id);
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
            $manifest = $new->manifest;

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

        // Helper: refresh ALL persisted payments for the order using the authoritative endpoint.
        $refreshAllPayments = function () use ($order) {
            $payments = $order->easypayPayments()->get();
            foreach ($payments as $p) {
                // Do not re-query or overwrite payments we've marked cancelled locally.
                if (($p->payment_status ?? null) === 'canceled') {
                    continue;
                }

                if (empty($p->payment_id)) {
                    continue;
                }

                try {
                    $single = (new \App\Services\EasypayService)->getSinglePayment($p->payment_id);
                } catch (\Throwable $e) {
                    $single = null;
                }

                if (is_array($single) && ! empty($single)) {
                    $attrs = [
                        'payment_status' => data_get($single, 'payment_status') ?? data_get($single, 'payment.status') ?? $p->payment_status,
                        'paid_at' => data_get($single, 'paid_at') ? \Carbon\Carbon::parse(data_get($single, 'paid_at')) : $p->paid_at,
                        'mb_entity' => data_get($single, 'method.entity') ?? data_get($single, 'payment.entity') ?? $p->mb_entity,
                        'mb_reference' => data_get($single, 'method.reference') ?? data_get($single, 'payment.reference') ?? $p->mb_reference,
                        'mb_expiration_time' => data_get($single, 'multibanco.expiration_time') ? \Carbon\Carbon::parse(data_get($single, 'multibanco.expiration_time')) : $p->mb_expiration_time,
                        'iban' => data_get($single, 'method.sdd_mandate.iban') ?? data_get($single, 'method.sdd_mandate') ?? $p->iban,
                        'raw_response' => $single,
                    ];

                    try {
                        $p->update(array_filter($attrs, fn ($v) => ! is_null($v)));
                    } catch (\Throwable $e) {
                        logger()->warning('EasypayOrchestration: failed to refresh payment', ['payment_id' => $p->payment_id, 'err' => $e->getMessage()]);
                    }
                }
            }

            // If any payment now reports authoritative 'paid', mark the order paid and persist details
            $paid = $order->easypayPayments()->whereIn('payment_status', ['paid', 'success'])->latest('created_at')->first();
            if ($paid && ($paid->payment_status === 'paid' || $paid->payment_status === 'success')) {
                try {
                    $order->markAsPaid('easypay', ['payment_id' => $paid->payment_id]);
                } catch (\Throwable $e) {
                    logger()->warning('EasypayOrchestration: markAsPaid failed', ['order_id' => $order->id, 'err' => $e->getMessage()]);
                }

                // ensure stored payment reflects paid_at/raw_response
                if (empty($paid->paid_at) && data_get($paid->raw_response, 'paid_at')) {
                    $paid->paid_at = \Carbon\Carbon::parse(data_get($paid->raw_response, 'paid_at'));
                    $paid->save();
                }

                return true;
            }

            return false;
        };

        // Helper: cancel ALL active checkout sessions for the order (best-effort)
        // After cancelling, fetch the authoritative checkout details and persist them to DB
        $cancelAllSessions = function () use ($order) {
            $active = $order->easypayCheckoutSessions()->where('is_active', true)->get();
            foreach ($active as $s) {
                try {
                    $res = \App\Services\EasypayService::cancelCheckout($s->checkout_id);
                    $s->update(['is_active' => false, 'status' => ($res['ok'] ? 'canceled' : 'error'), 'in_error' => $res['ok'] ? false : true]);
                } catch (\Throwable $e) {
                    $s->update(['is_active' => false, 'status' => 'error', 'in_error' => true]);
                    logger()->warning('EasypayOrchestration: failed to cancel checkout', ['checkout_id' => $s->checkout_id, 'err' => $e->getMessage()]);
                }

                // Best-effort: fetch checkout details from Easypay and persist the response (include status mapping)
                try {
                    $f = \App\Services\EasypayService::fetchCheckout($s->checkout_id);
                    if (! empty($f) && array_key_exists('body', $f) && ! empty($f['body'])) {
                        $body = is_array($f['body']) ? $f['body'] : json_decode((string) $f['body'], true);

                        // persist raw response
                        $s->message = json_encode($body);

                        // Map authoritative checkout status into the session row when available
                        $remoteStatus = data_get($body, 'checkout.status') ?? data_get($body, 'status');
                        if (! empty($remoteStatus) && is_string($remoteStatus)) {
                            // Do not overwrite authoritative local state produced by cancellation/error.
                            // Only apply remote status when the session is still in a neutral/active state.
                            if (empty($s->status) || in_array($s->status, ['pending', 'created'], true)) {
                                $s->status = $remoteStatus;
                                // maintain `is_active` semantics: pending/created => active, otherwise inactive
                                $s->is_active = in_array($remoteStatus, ['pending', 'created'], true);
                                $s->in_error = $remoteStatus === 'failed';
                            } else {
                                // keep local authoritative status (e.g. 'canceled'/'error') but keep remote in message
                                logger()->debug('EasypayOrchestration: preserving local session.status over remote', ['checkout_id' => $s->checkout_id, 'local_status' => $s->status, 'remote_status' => $remoteStatus]);
                            }
                        }

                        // If the remote returned explicit checkout/session ids, persist them too
                        $s->checkout_id = data_get($body, 'checkout.id') ?? $s->checkout_id;
                        $s->session_id = data_get($body, 'checkout.session') ?? data_get($body, 'session') ?? $s->session_id;

                        // If HTTP-level status indicates error, persist as error_code (best-effort)
                        if (! empty($f['status']) && $f['status'] >= 400) {
                            $s->error_code = $f['status'];
                        }

                        $s->save();
                    }
                } catch (\Throwable $e) {
                    logger()->warning('EasypayOrchestration: failed to fetch checkout after cancel', ['checkout_id' => $s->checkout_id, 'err' => $e->getMessage()]);
                }
            }
        };

        // Codes that should attempt a full refresh + recreate session
        $recreateCodes = ['checkout-expired', 'checkout-canceled', 'generic-error', 'payment-failure'];

        if ($code === 'checkout-expired') {
            // Refresh persisted payments first — if any is authoritative/paid, short-circuit
            if ($refreshAllPayments()) {
                return ['action' => 'already-paid', 'message' => t('checkout.pay.already_paid')];
            }

            // Cancel any active sessions and create a new one
            $cancelAllSessions();
            $payload = $order->easypayPayload ?? \App\Services\EasypayService::createOrGetPayload($order);
            $new = \App\Services\EasypayService::createCheckoutSession($payload);
            $manifest = $new->manifest;

            return $manifest && $new->is_active ? ['action' => 'new-manifest', 'manifest' => $manifest] : ['action' => 'error', 'message' => self::buildPayUnavailableMessage($new->message ?? null)];
        }

        if ($code === 'already-paid') {
            // Always attempt to refresh all payments for the order (paymentId may be absent)
            if ($refreshAllPayments()) {
                return ['action' => 'already-paid', 'message' => t('checkout.pay.already_paid')];
            }

            // If the SDK supplied a specific payment id, try to refresh it explicitly
            if ($paymentId) {
                $single = (new \App\Services\EasypayService)->getSinglePayment($paymentId);
                if (is_array($single) && ! empty($single)) {
                    $attrs = [
                        'payment_id' => data_get($single, 'id') ?? $paymentId,
                        'checkout_id' => $checkoutId,
                        'order_id' => $order->id,
                        'payment_status' => data_get($single, 'payment_status') ?? data_get($single, 'payment.status'),
                        'paid_at' => data_get($single, 'paid_at') ? \Carbon\Carbon::parse(data_get($single, 'paid_at')) : null,
                        'raw_response' => $single,
                    ];

                    $existing = \App\Models\EasypayPayment::where('payment_id', $attrs['payment_id'])->where('order_id', $order->id)->first();
                    if ($existing) {
                        // Do not overwrite a payment we've explicitly marked as canceled locally.
                        if (($existing->payment_status ?? null) === 'canceled') {
                            logger()->debug('EasypayOrchestration: skipping update of locally-cancelled payment', ['payment_id' => $existing->payment_id, 'order_id' => $order->id]);
                        } else {
                            $existing->fill(array_filter($attrs, fn ($v) => ! is_null($v)));
                            $existing->save();
                        }
                    } else {
                        \App\Models\EasypayPayment::create($attrs);
                    }

                    $stored = \App\Models\EasypayPayment::where('payment_id', $attrs['payment_id'])->where('order_id', $order->id)->first();
                    $status = $stored ? $stored->payment_status : ($attrs['payment_status'] ?? null);

                    if ($status === 'paid') {
                        $order->is_paid = true;
                        $order->status = 'PROCESSING';
                        $order->save();

                        return ['action' => 'already-paid', 'message' => t('checkout.pay.already_paid')];
                    }
                }
            }

            // No authoritative paid payment found — cancel sessions and recreate
            $cancelAllSessions();
            $payload = $order->easypayPayload ?? \App\Services\EasypayService::createOrGetPayload($order);
            $new = \App\Services\EasypayService::createCheckoutSession($payload);
            $manifest = $new->manifest;

            return $manifest && $new->is_active ? ['action' => 'new-manifest', 'manifest' => $manifest] : ['action' => 'error', 'message' => self::buildPayUnavailableMessage($new->message ?? null)];
        }

        if (in_array($code, $recreateCodes, true)) {
            // Always attempt to cancel existing sessions first (best-effort).
            $cancelAllSessions();

            // Then refresh all payments — if any authoritative paid exists, short-circuit
            if ($refreshAllPayments()) {
                return ['action' => 'already-paid', 'message' => t('checkout.pay.already_paid')];
            }

            // Recreate a fresh session for the client to start
            $payload = $order->easypayPayload ?? \App\Services\EasypayService::createOrGetPayload($order);
            $new = \App\Services\EasypayService::createCheckoutSession($payload);
            $manifest = $new->manifest;

            return $manifest && $new->is_active ? ['action' => 'new-manifest', 'manifest' => $manifest] : ['action' => 'error', 'message' => self::buildPayUnavailableMessage($new->message ?? null)];
        }

        return ['action' => 'error', 'message' => t('checkout.pay.unavailable')];
    }
}
