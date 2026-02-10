<?php

namespace App\Http\Controllers;

use App\Models\EasypayPayment;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;

/**
 * Minimal test-only helpers used by Cypress E2E.
 * Routes are registered only in local/testing environments.
 */
class TestHelpersController extends Controller
{
    public function seedOrder(Request $request)
    {
        if (! app()->environment(['local', 'testing'])) {
            abort(404);
        }

        $paymentId = $request->input('payment_id', 'cypress-'.uniqid());
        $paymentStatus = $request->input('payment_status', 'pending');

        // Create a predictable user for Cypress to authenticate with
        $password = $request->input('password', 'password');
        $user = User::factory()->create([
            'email' => $request->input('email', 'cypress+'.uniqid().'@example.test'),
            'password' => Hash::make($password),
        ]);

        $order = Order::factory()->for($user)->create([
            'status' => 'WAITING_PAYMENT',
            'is_paid' => $paymentStatus === 'paid',
        ]);

        // Optionally attach an EasypayPayment record
        EasypayPayment::create([
            'payment_id' => $paymentId,
            'order_id' => $order->id,
            'payment_status' => $paymentStatus,
            'mb_entity' => $request->input('mb_entity'),
            'mb_reference' => $request->input('mb_reference'),
            'mb_expiration' => $request->input('mb_expiration'),
            'iban' => $request->input('iban'),
            'paid_at' => $paymentStatus === 'paid' ? now() : null,
        ]);

        // Optionally seed an active checkout session/manifest for E2E (test-only)
        if ($request->filled('manifest')) {
            $manifest = $request->input('manifest');
            \App\Models\EasypayCheckoutSession::create([
                'order_id' => $order->id,
                'payload_id' => null,
                'checkout_id' => $request->input('checkout_id') ?? ('cypress-chk-'.bin2hex(random_bytes(6))),
                'session_id' => $request->input('session_id') ?? null,
                'is_active' => true,
                'status' => 'pending',
                'message' => is_string($manifest) ? $manifest : json_encode($manifest),
            ]);
        }

        // Create a one-time login token and return a URL Cypress can visit to set the session cookie
        $token = bin2hex(random_bytes(12));
        Cache::put('cypress:login:'.$token, $user->id, now()->addMinutes(10));

        return response()->json([
            'ok' => true,
            'order_id' => $order->id,
            'order_uuid' => $order->uuid,
            'payment_id' => $paymentId,
            'user' => [
                'email' => $user->email,
                'password' => $password,
            ],
            'login_url' => url('/__cypress/login/'.$token),
        ]);
    }

    public function loginWithToken(Request $request, $token)
    {
        if (! app()->environment(['local', 'testing'])) {
            abort(404);
        }

        $userId = Cache::pull('cypress:login:'.$token);
        if (empty($userId)) {
            abort(404);
        }

        Auth::loginUsingId($userId);
        session()->regenerate();

        // Return a tiny page — visiting this page sets the session cookie in the browser
        return response('<html><body>ok</body></html>')->header('Content-Type', 'text/html');
    }

    public function mockEasypay(Request $request)
    {
        if (! app()->environment(['local', 'testing'])) {
            abort(404);
        }

        $paymentId = $request->input('payment_id');
        $response = $request->input('response');

        if (empty($paymentId) || empty($response)) {
            return response()->json(['ok' => false, 'message' => 'payment_id and response required'], 422);
        }

        // Cache the mocked single-payment response for a short TTL
        Cache::put('easypay:test_single:'.$paymentId, $response, now()->addMinutes(10));

        return response()->json(['ok' => true, 'payment_id' => $paymentId]);
    }

    /**
     * Render a simple page with a server-side flash (test-only).
     * Used by Cypress to assert the server-rendered flash close button works.
     * Supports ?type=success|error|warning|info (defaults to success).
     */
    public function flash(Request $request)
    {
        if (! app()->environment(['local', 'testing'])) {
            abort(404);
        }

        $message = $request->input('message', 'Test server flash');
        $type = $request->input('type', 'success');
        $allowed = ['success', 'error', 'warning', 'info'];
        if (! in_array($type, $allowed, true)) {
            $type = 'success';
        }

        // Flash for the next request (keeps parity with typical redirects) and also
        // put the value into the session for immediate rendering in the same request
        // (useful for the test helper which returns a view directly).
        session()->flash($type, $message);
        session()->put($type, $message);
        // Ensure the value is available in the current request (useful for direct-render helper)
        session()->now($type, $message);

        // DEBUG: log what the controller sees (removed after debugging)
        logger()->info('cypress.flash.invoke', ['type' => $type, 'message' => $message, 'session_now' => session($type)]);

        // Return the welcome view which includes the global layout so the flash is visible
        return view('welcome');
    }
}
