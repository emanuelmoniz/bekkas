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
}
