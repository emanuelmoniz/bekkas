<?php

namespace App\Services;

use App\Models\EasypayCheckoutSession;
use App\Models\EasypayPayload;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EasypayService
{
    protected string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('easypay.base_url', env('EASYPAY_BASE_URL', 'https://api.test.easypay.pt/2.0')), '/');
    }

    /**
     * Fetch single payment details from Easypay API
     */
    public function getSinglePayment(string $paymentId): ?array
    {
        // Test override: when running locally/testing, allow Cypress to inject
        // a canned response via the cache so E2E can control server-side calls.
        // This path is guarded and only active in non-production environments.
        if (app()->environment(['local', 'testing'])) {
            try {
                /** @var \Illuminate\Support\Facades\Cache $cache */
                $mock = \Illuminate\Support\Facades\Cache::get('easypay:test_single:'.$paymentId);
                if (! empty($mock)) {
                    return $mock;
                }
            } catch (\Throwable $e) {
                // swallow — continue to real request if cache lookup fails
                Log::debug('EasypayService test-mock cache lookup failed', ['err' => $e->getMessage()]);
            }
        }

        $url = $this->baseUrl.'/single/'.rawurlencode($paymentId);

        $response = Http::withHeaders([
            'AccountId' => config('easypay.id', env('EASYPAY_ID')),
            'ApiKey' => config('easypay.api_key', env('EASYPAY_API_KEY')),
            'Accept' => 'application/json',
        ])->timeout(10)->get($url);

        try {
            $response->throw();
        } catch (RequestException $e) {
            Log::warning('Easypay getSinglePayment failed', ['payment_id' => $paymentId, 'error' => $e->getMessage()]);

            return null;
        }

        return $response->json();
    }

    /**
     * Build the checkout payload for an Order
     */
    public static function buildPayload(Order $order): array
    {
        $methods = json_decode(config('easypay.payment_methods', '[]'), true) ?: [];

        $items = $order->items->map(function ($it) {
            return [
                'description' => optional($it->product->translation())->name ?? "Product #{$it->product_id}",
                'quantity' => (int) $it->quantity,
                'key' => $it->product?->uuid ?? (string) $it->product_id,
                'value' => round($it->total_gross, 2),
            ];
        })->toArray();

        // Add shipping tier as an item when the order has a shipping cost.
        // Do NOT add an item for free shipping (shipping_gross === 0).
        if (! empty($order->shipping_gross) && round($order->shipping_gross, 2) > 0) {
            $shippingDescription = $order->shipping_tier_name ?? 'Shipping';

            // Try to resolve a ShippingTier id by name (best-effort). If not found
            // fall back to a stable string key made from the name.
            $shippingTier = \App\Models\ShippingTier::where('name_en', $shippingDescription)
                ->orWhere('name_pt', $shippingDescription)
                ->first();

            $shippingKey = $shippingTier ? (string) $shippingTier->id : ('shipping:'.\Illuminate\Support\Str::slug($shippingDescription ?? 'shipping'));

            $items[] = [
                'description' => $shippingDescription,
                'quantity' => 1,
                'key' => $shippingKey,
                'value' => round($order->shipping_gross, 2),
            ];
        }

        $mbTtl = (int) config('easypay.mb_ttl', 172800);
        $expiration = Carbon::now()->addSeconds($mbTtl)->setTimezone('UTC')->format('Y-m-d\TH:i:s\Z');

        $phone = optional($order->user)->phone ?: $order->address_phone ?? null;

        $userLangRaw = optional($order->user)->language ?? app()->getLocale();
        $userLangRaw = is_string($userLangRaw) ? $userLangRaw : '';
        $language = self::normalizeLanguage($userLangRaw) ?? strtoupper(substr($userLangRaw, 0, 2));

        return [
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
    }

    public static function createOrGetPayload(Order $order): ?EasypayPayload
    {
        $existing = $order->easypayPayload;
        if ($existing) {
            return $existing;
        }

        if (! config('easypay.enabled', false)) {
            Log::info('Easypay disabled — skipping payload creation', ['order_id' => $order->id]);

            return null;
        }

        $payloadArray = self::buildPayload($order);

        return EasypayPayload::create([
            'order_id' => $order->id,
            'payload' => $payloadArray,
        ]);
    }

    public static function createCheckoutSession(EasypayPayload $payload): EasypayCheckoutSession
    {
        $order = $payload->order;

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

        $url = rtrim(config('easypay.base_url'), '/').'/checkout';

        try {
            $toSend = $payload->payload ?? [];

            $resp = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'accountId' => config('easypay.id'),
                'apiKey' => config('easypay.api_key'),
            ])->timeout(10)->post($url, $toSend);

            if ($resp->status() === 201) {
                $body = $resp->json();

                $hasId = ! empty($body['id']);
                $hasSession = ! empty($body['session']) || ! empty($body['config']);

                $session->checkout_id = $body['id'] ?? null;
                $session->session_id = $body['session'] ?? ($body['config'] ?? null);
                $session->status = 'pending';
                $session->message = json_encode($body);

                if ($hasId && $hasSession) {
                    $session->is_active = true;
                    $session->in_error = false;
                    $session->error_code = null;
                } else {
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
            $session->save();

            Log::error('Easypay /checkout request failed', ['order_id' => $order->id, 'error' => $e->getMessage()]);
        }

        return $session;
    }

    public static function fetchCheckout(string $checkoutId): array
    {
        if (empty($checkoutId)) {
            return ['ok' => false, 'status' => 404, 'message' => 'missing checkout_id'];
        }

        if (! config('easypay.enabled', false)) {
            return ['ok' => false, 'status' => 503, 'message' => 'Easypay disabled'];
        }

        $url = rtrim(config('easypay.base_url'), '/').'/checkout/'.rawurlencode($checkoutId);

        try {
            $resp = Http::withHeaders(['Accept' => 'application/json', 'accountId' => config('easypay.id'), 'apiKey' => config('easypay.api_key')])->timeout(10)->get($url);
            $body = $resp->json() ?: null;

            return ['ok' => $resp->successful(), 'status' => $resp->status(), 'body' => $body];
        } catch (\Exception $e) {
            Log::warning('Easypay fetchCheckout failed', ['checkout_id' => $checkoutId, 'err' => $e->getMessage()]);

            return ['ok' => false, 'status' => 503, 'message' => $e->getMessage()];
        }
    }

    /**
     * Delete a single payment (DELETE /single/{id}). Returns an array with
     * keys: ok(bool), status(int), body(mixed|null).
     */
    public function deleteSinglePayment(string $paymentId): array
    {
        if (empty($paymentId)) {
            return ['ok' => false, 'status' => 400, 'body' => ['status' => 'error', 'message' => ['missing payment id']]];
        }

        if (! config('easypay.enabled', false)) {
            return ['ok' => false, 'status' => 503, 'body' => ['status' => 'error', 'message' => ['easypay disabled']]];
        }

        $url = rtrim(config('easypay.base_url'), '/').'/single/'.rawurlencode($paymentId);

        try {
            $resp = Http::withHeaders(['Accept' => 'application/json', 'accountId' => config('easypay.id'), 'apiKey' => config('easypay.api_key')])->timeout(10)->delete($url);

            if ($resp->status() === 204) {
                return ['ok' => true, 'status' => 204, 'body' => null];
            }

            $body = $resp->json() ?: $resp->body();
            return ['ok' => $resp->successful(), 'status' => $resp->status(), 'body' => $body];
        } catch (\Exception $e) {
            Log::warning('Easypay deleteSinglePayment failed', ['payment_id' => $paymentId, 'err' => $e->getMessage()]);
            return ['ok' => false, 'status' => 503, 'body' => ['status' => 'error', 'message' => [$e->getMessage()]]];
        }
    }

    /**
     * Cancel a checkout session (DELETE /checkout/{id}).
     * Returns ['ok' => bool, 'status' => int, 'body' => mixed]
     */
    public static function cancelCheckout(string $checkoutId): array
    {
        if (empty($checkoutId)) {
            return ['ok' => false, 'status' => 404, 'message' => 'missing checkout_id'];
        }

        if (! config('easypay.enabled', false)) {
            return ['ok' => false, 'status' => 503, 'message' => 'Easypay disabled'];
        }

        $url = rtrim(config('easypay.base_url'), '/').'/checkout/'.rawurlencode($checkoutId);

        try {
            $resp = Http::withHeaders([
                'Accept' => 'application/json',
                'accountId' => config('easypay.id'),
                'apiKey' => config('easypay.api_key'),
            ])->timeout(10)->delete($url);

            $status = $resp->status();
            try {
                $body = $resp->json();
            } catch (\Throwable $e) {
                $body = $resp->body();
            }

            return ['ok' => $status >= 200 && $status < 300, 'status' => $status, 'body' => $body];
        } catch (\Exception $e) {
            Log::error('Easypay DELETE /checkout/{id} request failed', ['checkout_id' => $checkoutId, 'error' => $e->getMessage()]);

            return ['ok' => false, 'status' => 502, 'message' => $e->getMessage()];
        }
    }

    private static function normalizeLanguage(?string $lang): ?string
    {
        if (empty($lang) || ! is_string($lang)) {
            return null;
        }

        $clean = preg_replace('/[^A-Za-z]/', '', $lang);
        if (preg_match('/([A-Za-z]{2})/', $clean, $m)) {
            return strtoupper($m[1]);
        }

        return null;
    }
}
