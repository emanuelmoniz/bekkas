<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Services\EasypayWebhookService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class EasypayController extends Controller
{
    /**
     * Receive Easypay generic notifications and log them.
     * Security: BasicAuth (recommended by Easypay) + optional header check.
     * Acknowledge immediately with HTTP 200 and delegate processing to a service.
     */
    public function generic(Request $request, EasypayWebhookService $service)
    {
        $cfgUser = config('easypay.webhook_user');
        $cfgPass = config('easypay.webhook_pass');

        $user = $request->getUser();
        $pass = $request->getPassword();

        // Enforce Basic auth when configured (allow local/testing without creds)
        if ($cfgUser || $cfgPass) {
            if (! (is_string($user) && is_string($pass) &&
                hash_equals((string) $cfgUser, (string) $user) &&
                hash_equals((string) $cfgPass, (string) $pass))) {
                Log::warning('easypay.webhook.auth_failed', ['ip' => $request->ip(), 'user' => $user]);

                return response()->json(['message' => 'Unauthorized'], 401);
            }
        } else {
            Log::debug('easypay.webhook.auth_skipped', ['env' => app()->environment()]);
        }

        // Optional header-based secret check
        $headerName = config('easypay.webhook_header');
        $secret = config('easypay.webhook_secret');
        if ($headerName && $secret) {
            $received = (string) $request->header($headerName, '');
            if (! hash_equals((string) $secret, $received)) {
                Log::warning('easypay.webhook.header_mismatch', ['header' => $headerName, 'received_prefix' => substr($received, 0, 8)]);

                return response()->json(['message' => 'Forbidden'], 403);
            }
        }

        $payload = $request->json()->all();

        // Acknowledge immediately with a simple OK (Easypay expects a 200 and plain response).
        $response = response('OK', 200)->header('Content-Type', 'text/plain');

        // Delegate processing (currently only logs). Keep errors isolated.
        try {
            $service->handleGeneric($payload, [
                'headers' => [$headerName => $request->header($headerName)],
                'ip' => $request->ip(),
            ]);
        } catch (\Throwable $e) {
            Log::error('easypay.webhook.handler_error', ['err' => $e->getMessage()]);
            // Do NOT surface internal errors to Easypay — we already acknowledged receipt.
        }

        return $response;
    }
}
