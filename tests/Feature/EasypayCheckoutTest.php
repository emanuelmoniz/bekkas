<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class EasypayCheckoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_payload_and_checkout_session_are_created_and_pay_page_is_accessible()
    {
        Config::set('easypay.enabled', true);
        Config::set('easypay.base_url', 'https://api.test.easypay.pt/2.0');

        Http::fake([
            'https://api.test.easypay.pt/2.0/checkout' => Http::response([
                'id' => '5db36b83-0664-4bc4-a760-7040ac3726f0',
                'session' => 'session-token-example',
                'status' => 'created',
            ], 201),
        ]);

        $user = User::factory()->create(['language' => 'pt']);
        $product = Product::factory()->create(['price' => 10.50, 'stock' => 10]);
        $address = Address::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)->withSession(['cart' => [$product->id => 1]])
            ->post(route('checkout.place'), ['address_id' => $address->id])
            ->assertRedirect(route('orders.index'));

        $order = Order::where('user_id', $user->id)->latest()->first();
        $this->assertNotNull($order);

        $this->assertDatabaseHas('easypay_payloads', ['order_id' => $order->id]);

        // Successful response should have created an ACTIVE session (id + session)
        $this->assertDatabaseHas('easypay_checkout_sessions', [
            'order_id' => $order->id,
            'in_error' => false,
            'checkout_id' => '5db36b83-0664-4bc4-a760-7040ac3726f0',
            'is_active' => true,
            'status' => 'pending',
        ]);

        // Ensure we updated canonical updated_at (we removed last_update_timestamp)
        $session = \App\Models\EasypayCheckoutSession::where('order_id', $order->id)->first();
        $this->assertNotNull($session->updated_at, 'updated_at should be set after Easypay response');
        $this->assertTrue($session->updated_at->gte($session->created_at));

        // Payload language must be ISO 639-1 alpha-2 uppercased (user was created with 'pt')
        $payload = \App\Models\EasypayPayload::where('order_id', $order->id)->first();
        $this->assertNotNull($payload);
        $this->assertEquals('PT', $payload->payload['customer']['language']);

        $response = $this->actingAs($user)->get(route('orders.pay', $order->uuid));
        $response->assertStatus(200);
        $response->assertSee('session-token-example');

        // NOTE: public session creation endpoint removed — sessions are created automatically at order placement (or by admin). Ensure the automatic session exists.
        $this->assertEquals(1, \App\Models\EasypayCheckoutSession::where('order_id', $order->id)->count());
    }

    public function test_checkout_session_stays_inactive_when_response_missing_id_or_session()
    {
        Config::set('easypay.enabled', true);
        Config::set('easypay.base_url', 'https://api.test.easypay.pt/2.0');

        // Return 201 but missing both id and session -> should be treated as error and remain inactive
        Http::fake([
            'https://api.test.easypay.pt/2.0/checkout' => Http::response(['status' => 'created'], 201),
        ]);

        $user = User::factory()->create(['language' => 'pt']);
        $product = Product::factory()->create(['price' => 10.50, 'stock' => 10]);
        $address = Address::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)->withSession(['cart' => [$product->id => 1]])
            ->post(route('checkout.place'), ['address_id' => $address->id])
            ->assertRedirect(route('orders.index'));

        $order = Order::where('user_id', $user->id)->latest()->first();
        $this->assertNotNull($order);

        $session = \App\Models\EasypayCheckoutSession::where('order_id', $order->id)->first();
        $this->assertNotNull($session);

        $this->assertFalse((bool) $session->is_active, 'Session must remain inactive when response lacks id/session');
        $this->assertTrue((bool) $session->in_error, 'Session must be marked in_error');
        $this->assertEquals(422, $session->error_code);
        $this->assertEquals('error', $session->status, 'Session status must be set to error when response lacks id/session');
    }

    public function test_payload_creation_normalizes_language()
    {
        Config::set('easypay.enabled', true);
        Config::set('easypay.base_url', 'https://api.test.easypay.pt/2.0');

        Http::fake([
            'https://api.test.easypay.pt/2.0/checkout' => Http::response([
                'id' => 'norm-123',
                'session' => 'norm-session-123',
                'status' => 'created',
            ], 201),
        ]);

        $user = User::factory()->create(['language' => 'pt-PT']);
        $product = Product::factory()->create(['price' => 3.5, 'stock' => 5]);
        $order = Order::factory()->for($user)->create(['status' => 'WAITING_PAYMENT', 'is_paid' => false]);

        // Act: create payload via the public API (idempotent creation path)
        $payload = \App\Services\EasypayService::createOrGetPayload($order);

        // Payload should be normalized at creation time
        $this->assertEquals('PT', $payload->payload['customer']['language']);

        // Now create checkout session and ensure we POST the normalized language
        \App\Services\EasypayService::createCheckoutSession($payload);

        \Illuminate\Support\Facades\Http::assertSent(function ($request) {
            $body = json_decode($request->body(), true);

            return isset($body['customer']['language']) && $body['customer']['language'] === 'PT';
        });

        $this->assertDatabaseHas('easypay_checkout_sessions', ['order_id' => $order->id, 'checkout_id' => 'norm-123', 'is_active' => true]);
    }

    public function test_no_payload_or_session_created_when_easypay_disabled_on_pay_page()
    {
        Config::set('easypay.enabled', false);

        $user = User::factory()->create(['language' => 'pt']);
        $order = Order::factory()->for($user)->create(['status' => 'WAITING_PAYMENT', 'is_paid' => false]);

        $this->assertDatabaseMissing('easypay_payloads', ['order_id' => $order->id]);
        $this->assertDatabaseMissing('easypay_checkout_sessions', ['order_id' => $order->id]);

        $resp = $this->actingAs($user)->get(route('orders.pay', $order->uuid));
        $resp->assertStatus(200);

        // No rows should have been created by visiting the pay page while disabled
        $this->assertDatabaseMissing('easypay_payloads', ['order_id' => $order->id]);
        $this->assertDatabaseMissing('easypay_checkout_sessions', ['order_id' => $order->id]);

        // The pay page should show the friendly unavailable message (use translation where available)
        $resp->assertSee(t('checkout.pay.unavailable') ?: 'Payment system is temporarily unavailable', false);
    }
}
