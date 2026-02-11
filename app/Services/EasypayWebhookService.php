<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

/**
 * Thin service to handle Easypay webhook payloads.
 * Business logic should be added here later (idempotency, verification, DB updates).
 */
class EasypayWebhookService
{
    /**
     * Handle generic notification payload.
     * For now: structured logging only (developer requested).
     *
     * @param array $payload
     * @param array $meta
     * @return void
     */
    public function handleGeneric(array $payload, array $meta = []): void
    {
        Log::info('easypay.webhook.generic', [
            'payload' => $payload,
            'meta' => array_merge($meta, ['env' => config('app.env')]),
        ]);

        // Only handle 'capture' → 'success' notifications for now
        $type = data_get($payload, 'type');
        $status = data_get($payload, 'status');

        if ($type === 'capture' && $status === 'success') {
            try {
                $this->handleCaptureSuccess($payload);
            } catch (\Throwable $e) {
                Log::error('easypay.webhook.capture_error', ['err' => $e->getMessage(), 'payload_id' => data_get($payload, 'id')] );
            }
        }
    }

    /**
     * Process capture-success generic notifications:
     *  - fetch authoritative single-payment
     *  - persist/update EasypayPayment
     *  - if remote status === 'paid' mark the related order as paid
     *  - fetch checkout details (when available) and persist session info
     */
    public function handleCaptureSuccess(array $payload): void
    {
        $paymentId = data_get($payload, 'id');
        if (empty($paymentId)) {
            Log::warning('easypay.webhook.capture_missing_id', ['payload' => $payload]);
            return;
        }

        $api = new EasypayService();
        $single = null;
        try {
            $single = $api->getSinglePayment($paymentId);
        } catch (\Throwable $e) {
            Log::warning('easypay.webhook.capture_get_single_failed', ['payment_id' => $paymentId, 'err' => $e->getMessage()]);
            return;
        }

        if (empty($single) || ! is_array($single)) {
            Log::warning('easypay.webhook.capture_no_remote', ['payment_id' => $paymentId]);
            return;
        }

        // Upsert the EasypayPayment row (mirror controller/service behaviour)
        $record = \App\Models\EasypayPayment::firstOrNew(['payment_id' => data_get($single, 'id') ?? $paymentId]);
        $record->checkout_id = data_get($single, 'checkout.id') ?? data_get($single, 'checkoutId') ?? $record->checkout_id;
        // attempt to infer order via existing checkout session when present
        if (empty($record->order_id) && $record->checkout_id) {
            $session = \App\Models\EasypayCheckoutSession::where('checkout_id', $record->checkout_id)->first();
            if ($session && $session->order_id) {
                $record->order_id = $session->order_id;
            }
        }

        $record->payment_status = data_get($single, 'payment_status') ?? data_get($single, 'payment.status') ?? $record->payment_status ?? 'pending';
        $record->paid_at = data_get($single, 'paid_at') ? \Carbon\Carbon::parse(data_get($single, 'paid_at')) : ($record->paid_at ?? null);
        $record->mb_entity = data_get($single, 'method.entity') ?? data_get($single, 'payment.entity') ?? $record->mb_entity;
        $record->mb_reference = data_get($single, 'method.reference') ?? data_get($single, 'payment.reference') ?? $record->mb_reference;
        $record->mb_expiration_time = data_get($single, 'multibanco.expiration_time') ? \Carbon\Carbon::parse(data_get($single, 'multibanco.expiration_time')) : ($record->mb_expiration_time ?? null);
        $record->iban = data_get($single, 'method.sdd_mandate.iban') ?? data_get($single, 'method.sdd_mandate') ?? $record->iban;
        $record->raw_response = $single;

        try {
            $record->save();
        } catch (\Throwable $e) {
            Log::warning('easypay.webhook.capture_persist_failed', ['payment_id' => $paymentId, 'err' => $e->getMessage()]);
        }

        // If remote authoritative status is 'paid' — ensure order is marked paid
        $remoteStatus = data_get($single, 'payment_status') ?? data_get($single, 'payment.status') ?? data_get($single, 'status');
        if ($remoteStatus === 'paid') {
            // Prefer order_id from the persisted payment row
            if ($record->order_id) {
                $order = \App\Models\Order::find($record->order_id);
                if ($order && ! $order->is_paid) {
                    try {
                        $order->markAsPaid('easypay', ['payment_id' => $record->payment_id]);
                        Log::info('easypay.webhook.capture_marked_paid', ['order_id' => $order->id, 'payment_id' => $record->payment_id]);
                    } catch (\Throwable $e) {
                        Log::warning('easypay.webhook.capture_mark_paid_failed', ['order_id' => $record->order_id, 'err' => $e->getMessage()]);
                    }
                }
            } else {
                // fallback: try to resolve order via existing persisted payment row
                $pr = \App\Models\EasypayPayment::where('payment_id', $paymentId)->first();
                if ($pr && $pr->order_id) {
                    $o = \App\Models\Order::find($pr->order_id);
                    if ($o && ! $o->is_paid) {
                        try {
                            $o->markAsPaid('easypay', ['payment_id' => $pr->payment_id]);
                            Log::info('easypay.webhook.capture_marked_paid_via_payment', ['order_id' => $o->id, 'payment_id' => $pr->payment_id]);
                        } catch (\Throwable $e) {
                            Log::warning('easypay.webhook.capture_mark_paid_failed_via_payment', ['order_id' => $pr->order_id, 'err' => $e->getMessage()]);
                        }
                    }
                }
            }
        }

        // If we have a checkout id, fetch authoritative checkout and persist session details
        $checkoutId = data_get($single, 'checkout.id') ?? data_get($single, 'checkoutId') ?? $record->checkout_id;
        if (! empty($checkoutId)) {
            try {
                $f = EasypayService::fetchCheckout($checkoutId);
                if (! empty($f) && array_key_exists('body', $f) && ! empty($f['body'])) {
                    $body = is_array($f['body']) ? $f['body'] : json_decode((string) $f['body'], true);

                    $session = \App\Models\EasypayCheckoutSession::where('checkout_id', $checkoutId)->first();
                    if (! $session) {
                        $session = \App\Models\EasypayCheckoutSession::create(['checkout_id' => $checkoutId, 'message' => json_encode($body), 'is_active' => false]);
                    } else {
                        $session->message = json_encode($body);
                        $session->checkout_id = data_get($body, 'checkout.id') ?? $session->checkout_id;
                        $session->session_id = data_get($body, 'checkout.session') ?? data_get($body, 'session') ?? $session->session_id;

                        $remoteStatus = data_get($body, 'checkout.status') ?? data_get($body, 'status');
                        if (! empty($remoteStatus) && is_string($remoteStatus)) {
                            if (empty($session->status) || in_array($session->status, ['pending', 'created'], true)) {
                                $session->status = $remoteStatus;
                                $session->is_active = in_array($remoteStatus, ['pending', 'created'], true);
                                $session->in_error = $remoteStatus === 'failed';
                            }
                        }

                        if (! empty($f['status']) && $f['status'] >= 400) {
                            $session->error_code = $f['status'];
                        }

                        $session->save();
                    }
                }
            } catch (\Throwable $e) {
                Log::warning('easypay.webhook.capture_fetch_checkout_failed', ['checkout_id' => $checkoutId, 'err' => $e->getMessage()]);
            }
        }
    }
}
