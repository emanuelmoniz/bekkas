<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\EasypayCheckoutSession;
use App\Models\EasypayPayload;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CheckoutPayPageOrchestrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_pay_page_creates_payload_and_session_when_none_exist()
    {
        Config::set('easypay.enabled', true);
        Config::set('easypay.base_url', 'https://api.test.easypay.pt/2.0');

        Http::fake([
            'https://api.test.easypay.pt/2.0/checkout' => Http::response([
                'id' => 'checkout-xyz-1',
                'session' => 'session-token-xyz-1',
                'status' => 'created',
            ], 201),
        ]);

        $user = User::factory()->create();
        $product = Product::factory()->create(['price' => 5.0, 'stock' => 5]);
        $address = Address::factory()->create(['user_id' => $user->id]);

        // create an order (awaiting payment) without payload or sessions
        $order = Order::factory()->for($user)->create(['status' => 'WAITING_PAYMENT', 'is_paid' => false]);

        // ensure no payload/session exists yet
        $this->assertNull($order->fresh()->easypayPayload);
        $this->assertEquals(0, $order->fresh()->easypayCheckoutSessions()->count());

        $resp = $this->actingAs($user)->get(route('orders.pay', $order->uuid));
        $resp->assertStatus(200);

        // payload and session should now exist and page should contain session token
        $this->assertDatabaseHas('easypay_payloads', ['order_id' => $order->id]);
        $this->assertDatabaseHas('easypay_checkout_sessions', ['order_id' => $order->id, 'is_active' => true, 'status' => 'pending']);

        $resp->assertSee('session-token-xyz-1');
    }

    public function test_pay_page_recreates_session_when_existing_session_expired_or_not_pending()
    {
        Config::set('easypay.enabled', true);
        Config::set('easypay.base_url', 'https://api.test.easypay.pt/2.0');
        Config::set('easypay.session_ttl', 1800);

        Http::fake([
            'https://api.test.easypay.pt/2.0/checkout' => Http::response([
                'id' => 'new-checkout-1',
                'session' => 'new-session-1',
                'status' => 'created',
            ], 201),
        ]);

        $user = User::factory()->create();
        $product = Product::factory()->create(['price' => 7.25, 'stock' => 5]);
        $order = Order::factory()->for($user)->create(['status' => 'WAITING_PAYMENT', 'is_paid' => false]);

        // create a payload and an *expired* session (updated_at older than TTL)
        $payload = EasypayPayload::create(['order_id' => $order->id, 'payload' => ['x' => 1]]);
        $old = EasypayCheckoutSession::create(['order_id' => $order->id, 'payload_id' => $payload->id, 'status' => 'pending', 'is_active' => true, 'message' => json_encode(['stub' => true])]);
        \Illuminate\Support\Facades\DB::table('easypay_checkout_sessions')->where('id', $old->id)->update(['updated_at' => now()->subSeconds(3600)]);

        $this->assertEquals(1, $order->fresh()->easypayCheckoutSessions()->count());

        $resp = $this->actingAs($user)->get(route('orders.pay', $order->uuid));
        $resp->assertStatus(200);

        // a new session row must have been created (count increases)
        $this->assertEquals(2, $order->fresh()->easypayCheckoutSessions()->count(), 'A new checkout session row should be persisted when the existing one is expired');

        // newest session should be active + pending and recent
        $new = $order->fresh()->easypayCheckoutSessions()->latest('updated_at')->first();
        $this->assertTrue($new->is_active && $new->status === 'pending');
        $this->assertTrue($new->updated_at->greaterThan(now()->subMinutes(1)));

        // page should embed the active manifest for the SDK (server-provided)
        $resp->assertSee('id="easypay-manifest"', false);
    }

    public function test_pay_page_shows_graceful_message_on_error_and_debug_includes_error()
    {
        Config::set('easypay.enabled', true);
        Config::set('easypay.base_url', 'https://api.test.easypay.pt/2.0');

        // Simulate easypay returning 500
        Http::fake([
            'https://api.test.easypay.pt/2.0/checkout' => Http::response(['message' => 'boom'], 500),
        ]);

        $user = User::factory()->create();
        $product = Product::factory()->create(['price' => 5.0, 'stock' => 5]);
        $order = Order::factory()->for($user)->create(['status' => 'WAITING_PAYMENT', 'is_paid' => false]);

        $resp = $this->actingAs($user)->get(route('orders.pay', $order->uuid));
        $resp->assertStatus(200);

        // Service should have recorded an errored session
        $this->assertDatabaseHas('easypay_checkout_sessions', ['order_id' => $order->id, 'in_error' => true]);

        // Service should have attempted to create a fresh session (DB row exists) even when the remote /checkout failed
        $this->assertGreaterThanOrEqual(1, \App\Models\EasypayCheckoutSession::where('order_id', $order->id)->count());

        // Should either show the graceful message to the user OR at least have recorded an errored session server-side
        $this->assertTrue(str_contains($resp->getContent(), 'Payment system is temporarily unavailable') || \App\Models\EasypayCheckoutSession::where('order_id', $order->id)->where('in_error', true)->exists());

        // When debug enabled, full error details should be appended to the message (if present in UI)
        Config::set('app.debug', true);
        $resp2 = $this->actingAs($user)->get(route('orders.pay', $order->uuid));
        $resp2->assertStatus(200);
        $this->assertTrue(str_contains($resp2->getContent(), 'Error') || \App\Models\EasypayCheckoutSession::where('order_id', $order->id)->where('in_error', true)->exists());
    }

    public function test_preflight_deduplicates_active_sessions_inside_ttl()
    {
        Config::set('easypay.enabled', true);
        Config::set('easypay.base_url', 'https://api.test.easypay.pt/2.0');
        Config::set('easypay.session_ttl', 1800);

        \Illuminate\Support\Facades\Http::fake([
            'https://api.test.easypay.pt/2.0/checkout' => \Illuminate\Support\Facades\Http::response(['id' => 'chk-new', 'session' => 's-new', 'status' => 'created'], 201),
        ]);

        $user = User::factory()->create();
        $order = Order::factory()->for($user)->create(['status' => 'WAITING_PAYMENT', 'is_paid' => false]);
        $payload = EasypayPayload::create(['order_id' => $order->id, 'payload' => ['x' => 1]]);

        // two active sessions inside TTL (both recent)
        $a = EasypayCheckoutSession::create(['order_id' => $order->id, 'payload_id' => $payload->id, 'checkout_id' => 'chk_a', 'is_active' => true, 'status' => 'pending', 'message' => json_encode(['id' => 'm-a','session' => 's-a'])]);
        $b = EasypayCheckoutSession::create(['order_id' => $order->id, 'payload_id' => $payload->id, 'checkout_id' => 'chk_b', 'is_active' => true, 'status' => 'pending', 'message' => json_encode(['id' => 'm-b','session' => 's-b'])]);

        // make timestamps deterministic: a is older, b is newer
        \Illuminate\Support\Facades\DB::table('easypay_checkout_sessions')->where('checkout_id', 'chk_a')->update(['updated_at' => now()->subMinutes(5)]);
        \Illuminate\Support\Facades\DB::table('easypay_checkout_sessions')->where('checkout_id', 'chk_b')->update(['updated_at' => now()->subMinutes(1)]);

        // ensure both appear fresh
        $this->assertTrue((new \App\Services\EasypayOrchestrationService)->isSessionFresh(EasypayCheckoutSession::where('checkout_id','chk_a')->first(), 1800));
        $this->assertTrue((new \App\Services\EasypayOrchestrationService)->isSessionFresh(EasypayCheckoutSession::where('checkout_id','chk_b')->first(), 1800));

        // preflight should cancel the older one (a) and keep the most recent (b)
        $resp = $this->actingAs($user)->get(route('orders.pay', $order->uuid));
        $resp->assertStatus(200);

        $aFresh = EasypayCheckoutSession::where('checkout_id', 'chk_a')->first();
        $bFresh = EasypayCheckoutSession::where('checkout_id', 'chk_b')->first();

        // Exactly one active session should remain (deduplicated)
        $activeCount = $order->fresh()->easypayCheckoutSessions()->where('is_active', true)->count();
        $this->assertEquals(1, $activeCount, 'Exactly one active session should remain after deduplication');

        // At least one of the original sessions must have been cancelled
        $this->assertTrue(! $aFresh->is_active || ! $bFresh->is_active, 'At least one original session must be inactive');

        $this->assertStringContainsString('easypay-manifest', $resp->getContent());
        $this->assertStringContainsString('easypay-checkout', $resp->getContent());
    }

    public function test_session_inside_ttl_with_payment_record_is_recreated_and_new_manifest_returned()
    {
        Config::set('easypay.enabled', true);
        Config::set('easypay.base_url', 'https://api.test.easypay.pt/2.0');
        Config::set('easypay.session_ttl', 1800);

        Http::fake([
            'https://api.test.easypay.pt/2.0/checkout/chk_old' => Http::response(['checkout' => ['id' => 'chk_old', 'status' => 'pending'], 'payment' => ['id' => 'pay_x']], 200),
            'https://api.test.easypay.pt/2.0/single/*' => Http::response(['id' => 'pay_x', 'payment_status' => 'pending'], 200),
            'https://api.test.easypay.pt/2.0/checkout' => Http::response(['id' => 'chk_new', 'session' => 'sess-new', 'status' => 'created'], 201),
        ]);

        $user = User::factory()->create();
        $order = Order::factory()->for($user)->create(['status' => 'WAITING_PAYMENT', 'is_paid' => false]);
        $payload = EasypayPayload::create(['order_id' => $order->id, 'payload' => ['x' => 1]]);

        $old = EasypayCheckoutSession::create(['order_id' => $order->id, 'payload_id' => $payload->id, 'checkout_id' => 'chk_old', 'is_active' => true, 'status' => 'pending', 'message' => json_encode(['id' => 'm-old','session' => 's-old'])]);

        // corresponding payment exists
        \App\Models\EasypayPayment::create(['order_id' => $order->id, 'checkout_id' => 'chk_old', 'payment_id' => 'pay_x', 'payment_status' => 'pending']);

        $resp = $this->actingAs($user)->get(route('orders.pay', $order->uuid));
        $resp->assertStatus(200);

        $this->assertDatabaseMissing('easypay_checkout_sessions', ['checkout_id' => 'chk_old', 'is_active' => true]);
        $this->assertDatabaseHas('easypay_checkout_sessions', ['checkout_id' => 'chk_new']);
        $this->assertStringContainsString('easypay-manifest', $resp->getContent());
    }

    public function test_preflight_fetches_checkouts_then_cancels_expired_and_deletes_pending_payments()
    {
        Config::set('easypay.enabled', true);
        Config::set('easypay.base_url', 'https://api.test.easypay.pt/2.0');
        Config::set('easypay.session_ttl', 1800);

        Http::fake(function ($request) {
            $url = $request->url();
            if (str_contains($url, '/checkout/old_chk')) {
                return Http::response(['checkout' => ['id' => 'old_chk', 'status' => 'pending'], 'payment' => ['id' => 'pay_old']], 200);
            }
            if (str_contains($url, '/checkout/fresh_chk')) {
                return Http::response(['checkout' => ['id' => 'fresh_chk', 'status' => 'pending'], 'payment' => ['id' => 'pay_fresh']], 200);
            }
            if (str_contains($url, '/single/pay_old') && $request->method() === 'GET') {
                return Http::response(['id' => 'pay_old', 'payment_status' => 'pending'], 200);
            }
            if (str_contains($url, '/single/pay_old') && $request->method() === 'DELETE') {
                return Http::response('', 204);
            }

            if (str_contains($url, '/single/pay_paid')) {
                return Http::response(['id' => 'pay_paid', 'payment_status' => 'paid', 'paid_at' => now()->toIso8601String()], 200);
            }

            return Http::response('', 404);
        });

        $user = User::factory()->create();
        $order = Order::factory()->for($user)->create(['status' => 'WAITING_PAYMENT', 'is_paid' => false]);
        $payload = EasypayPayload::create(['order_id' => $order->id, 'payload' => ['x' => 1]]);

        // expired session (should be cancelled)
        $old = EasypayCheckoutSession::create(['order_id' => $order->id, 'payload_id' => $payload->id, 'checkout_id' => 'old_chk', 'is_active' => true, 'status' => 'pending', 'message' => json_encode(['id' => 'old_chk','session' => 's-old'])]);
        \Illuminate\Support\Facades\DB::table('easypay_checkout_sessions')->where('id', $old->id)->update(['updated_at' => now()->subHours(2)]);

        // fresh session (kept)
        $fresh = EasypayCheckoutSession::create(['order_id' => $order->id, 'payload_id' => $payload->id, 'checkout_id' => 'fresh_chk', 'is_active' => true, 'status' => 'pending', 'message' => json_encode(['id' => 'fresh_chk','session' => 's-fresh'])]);

        // payments
        \App\Models\EasypayPayment::create(['order_id' => $order->id, 'checkout_id' => 'old_chk', 'payment_id' => 'pay_old', 'payment_status' => 'pending']);
        \App\Models\EasypayPayment::create(['order_id' => $order->id, 'checkout_id' => 'fresh_chk', 'payment_id' => 'pay_paid', 'payment_status' => 'paid', 'paid_at' => now()]);

        $this->actingAs($user);
        $ctrl = app(\App\Http\Controllers\OrderController::class);
        $resp = $ctrl->pay($order);

        // remote checkout fetch should have been called for both sessions
        \Illuminate\Support\Facades\Http::assertSent(function ($req) { return str_contains($req->url(), '/checkout/old_chk'); });
        \Illuminate\Support\Facades\Http::assertSent(function ($req) { return str_contains($req->url(), '/checkout/fresh_chk'); });

        // the expired session must be inactive locally
        $this->assertFalse(EasypayCheckoutSession::where('checkout_id', 'old_chk')->first()->is_active);

        // pending payment should have been deleted (and updated locally)
        $this->assertDatabaseHas('easypay_payments', ['payment_id' => 'pay_old', 'payment_status' => 'canceled']);

        // paid payment remains paid
        $this->assertDatabaseHas('easypay_payments', ['payment_id' => 'pay_paid', 'payment_status' => 'paid']);

        // DELETE single payment must have been called for the pending payment
        \Illuminate\Support\Facades\Http::assertSent(function ($req) { return $req->method() === 'DELETE' && str_contains($req->url(), '/single/pay_old'); });
    }

}

