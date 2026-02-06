<?php

namespace App\Services;

use App\Models\EasypayPayload;
use App\Models\EasypayCheckoutSession;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EasypayService
{
    public static function buildPayload(Order $order): array
    {
        $methods = json_decode(config('easypay.payment_methods', '[]'), true) ?: [];

        // Build items from order items (snapshot values)
        $items = $order->items->map(function ($it) {
            return [
                'description' => optional($it->product->translation())->name ?? "Product #{$it->product_id}",
                'quantity' => (int) $it->quantity,
                'key' => $it->product?->uuid ?? (string) $it->product_id,
                'value' => round($it->total_gross, 2),
            ];
        })->toArray();

        $mbTtl = (int) config('easypay.mb_ttl', 172800);
        $expiration = Carbon::now()->addSeconds($mbTtl)->setTimezone('UTC')->format('Y-m-d\TH:i:s\Z');

        // Customer phone fallback: user profile -> order address -> null
        $phone = optional($order->user)->phone ?: $order->address_phone ?? null;

        // Normalize language to ISO 639-1 alpha-2 (e.g. "PT", "EN") at payload-creation time
        $userLangRaw = optional($order->user)->language ?? app()->getLocale();
        $userLangRaw = is_string($userLangRaw) ? $userLangRaw : '';
        $language = self::normalizeLanguage($userLangRaw) ?? strtoupper(substr($userLangRaw, 0, 2));

        $payload = [
            'type' => ['single'],
            'payment' => [
                'methods' => $methods,
                'type' => 'sale',
                'capture' => [
                    'descriptive' => config('app.name', 'BEKKAS - 3D Printing Studio'),
                ],
                'currency' => 'EUR',
                'capture_now' => true,
                'multibanco' => [
                    'product' => 'CHECKDIGIT',
                    'expiration_time' => $expiration,
                ],
            ],
            'order' => [
                'items' => $items,
                'key' => $order->uuid,
                'value' => round($order->total_gross, 2),
            ],
            'customer' => [
                'name' => optional($order->user)->name ?: null,
                'email' => optional($order->user)->email ?: null,
                'phone' => $phone,
                'language' => $language,
                'fiscal_number' => $order->address_nif ?: null,
                'key' => (string) $order->user_id,
            ],
        ];

        return $payload;
    }

    /**
     * Create or return existing payload for the order.
     *
     * IMPORTANT: when Easypay is disabled this method will NOT create a new payload and
     * will return null (callers must handle the absence). Existing payloads are still
     * returned so previously-created data remains readable.
     */
    public static function createOrGetPayload(Order $order): ?EasypayPayload
    {
        $existing = $order->easypayPayload;
        if ($existing) return $existing;

        // Do not create new payloads when the integration is disabled via config/env
        if (! config('easypay.enabled', false)) {
            \Log::info('Easypay disabled — skipping payload creation', ['order_id' => $order->id]);
            return null;
        }

        $payloadArray = self::buildPayload($order);

        return EasypayPayload::create([
            'order_id' => $order->id,
            'payload' => $payloadArray,
        ]);
    }

    /**
     * Create a DB checkout session record, call Easypay /checkout and update the record with the response.
     */
    public static function createCheckoutSession(EasypayPayload $payload): EasypayCheckoutSession
    {
        $order = $payload->order;

        // Create session record as INACTIVE by default — it becomes active only when we receive both id and session
        // Use Eloquent's created_at/updated_at instead of custom timestamp columns.
        $session = EasypayCheckoutSession::create([
            'order_id' => $order->id,
            'payload_id' => $payload->id,
            'is_active' => false,
            'in_error' => false,
        ]);

        if (! config('easypay.enabled', false)) {
            $session->update(['status' => 'disabled', 'message' => 'Easypay disabled in config']);
            return $session;
        }

        $url = config('easypay.base_url') . '/checkout';

        try {
            // Send the stored payload as-is. Payload creation is responsible for correct values (do not mutate stored payload here).
            $toSend = $payload->payload ?? [];

            $resp = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                // pass accountId/apiKey as headers so they can be adapted easily if vendor requires different auth
                'accountId' => config('easypay.id'),
                'apiKey' => config('easypay.api_key'),
            ])->timeout(10)->post($url, $toSend);

            // rely on updated_at (Eloquent) instead of last_update_timestamp
            // (we previously set last_update_timestamp = now() here)

            if ($resp->status() === 201) {
                $body = $resp->json();

                // Require both id and session token to consider the session active
                $hasId = ! empty($body['id']);
                $hasSession = ! empty($body['session']) || ! empty($body['config']);

                $session->checkout_id = $body['id'] ?? null;
                $session->session_id = $body['session'] ?? ($body['config'] ?? null);
                // Successful creation => mark as pending in our DB (Easypay's raw status is kept in message)
                $session->status = 'pending';
                $session->message = json_encode($body);

                if ($hasId && $hasSession) {
                    $session->is_active = true;
                    $session->in_error = false;
                    $session->error_code = null;
                } else {
                    // Treat missing critical fields as an error — keep is_active = false
                    $session->is_active = false;
                    $session->in_error = true;
                    $session->error_code = 422;
                    $session->status = 'error';
                    Log::warning('Easypay /checkout returned 201 but missing id/session', ['order_id' => $order->id, 'body' => $body]);
                }

                $session->save();
            } else {
                $session->in_error = true;
                $session->error_code = $resp->status();
                $session->status = 'error';
                $session->message = json_encode($resp->json() ?: $resp->body());
                $session->save();
                Log::warning('Easypay /checkout returned non-201', ['order_id' => $order->id, 'status' => $resp->status(), 'body' => $resp->body()]);
            }
        } catch (\Exception $e) {
            $session->in_error = true;
            $session->status = 'error';
            $session->message = $e->getMessage();
            // rely on Eloquent's updated_at instead of last_update_timestamp
            $session->save();

            Log::error('Easypay /checkout request failed', ['order_id' => $order->id, 'error' => $e->getMessage()]);
        }

        return $session;
    }

    /**
     * Fetch checkout details from Easypay `/checkout/{checkoutId}`
     *
     * Returns an array: ['ok' => bool, 'status' => int, 'body' => mixed, 'message' => string?]
     */
    public static function fetchCheckout(string $checkoutId): array
    {
        if (empty($checkoutId)) {
            return ['ok' => false, 'status' => 404, 'message' => 'missing checkout_id'];
        }

        if (! config('easypay.enabled', false)) {
            return ['ok' => false, 'status' => 503, 'message' => 'Easypay disabled'];
        }

        $url = config('easypay.base_url') . '/checkout/' . rawurlencode($checkoutId);

        try {
            $resp = Http::withHeaders([
                'Accept' => 'application/json',
                'accountId' => config('easypay.id'),
                'apiKey' => config('easypay.api_key'),
            ])->timeout(10)->get($url);

            $status = $resp->status();

            try {
                $body = $resp->json();
            } catch (\Throwable $e) {
                $body = $resp->body();
            }

            return ['ok' => $status >= 200 && $status < 300, 'status' => $status, 'body' => $body];
        } catch (\Exception $e) {
            Log::error('Easypay /checkout/{id} request failed', ['checkout_id' => $checkoutId, 'error' => $e->getMessage()]);
            return ['ok' => false, 'status' => 502, 'message' => $e->getMessage()];
        }
    }

    /**
     * Normalize language values to ISO 639-1 alpha-2 uppercase (e.g. "PT", "EN").
     * Accepts values like "pt-PT", "pt_PT", "pt", "pt.pt" and returns "PT".
     */
    private static function normalizeLanguage(?string $lang): ?string
    {
        if (empty($lang) || ! is_string($lang)) {
            return null;
        }

        // Trim and replace separators with nothing, then capture first two alpha chars
        $clean = preg_replace('/[^A-Za-z]/', '', $lang);
        if (preg_match('/([A-Za-z]{2})/', $clean, $m)) {
            return strtoupper($m[1]);
        }

        return null;
    }
}
