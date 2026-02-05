<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use App\Models\Address;
use App\Models\Order;
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

        $this->actingAs($user)->withSession(['cart' => [ $product->id => 1 ]])
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
        ]);

        // Payload language must be ISO 639-1 alpha-2 uppercased (user was created with 'pt')
        $payload = \App\Models\EasypayPayload::where('order_id', $order->id)->first();
        $this->assertNotNull($payload);
        $this->assertEquals('PT', $payload->payload['customer']['language']);

        $response = $this->actingAs($user)->get(route('orders.pay', $order->uuid));
        $response->assertStatus(200);
        $response->assertSee('session-token-example');

        // Create an additional checkout session via the new AJAX endpoint
        $createResp = $this->actingAs($user)->postJson(route('orders.pay.session', $order->uuid));
        $createResp->assertStatus(201);
        $createResp->assertJson(['ok' => true]);
        $this->assertStringContainsString('session-token-example', $createResp->json('html'));

        $this->assertEquals(2, \App\Models\EasypayCheckoutSession::where('order_id', $order->id)->count());
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

        $this->actingAs($user)->withSession(['cart' => [ $product->id => 1 ]])
            ->post(route('checkout.place'), ['address_id' => $address->id])
            ->assertRedirect(route('orders.index'));

        $order = Order::where('user_id', $user->id)->latest()->first();
        $this->assertNotNull($order);

        $session = \App\Models\EasypayCheckoutSession::where('order_id', $order->id)->first();
        $this->assertNotNull($session);

        $this->assertFalse((bool) $session->is_active, 'Session must remain inactive when response lacks id/session');
        $this->assertTrue((bool) $session->in_error, 'Session must be marked in_error');
        $this->assertEquals(422, $session->error_code);
    }

    public function test_user_can_fetch_checkout_info_successfully()
    {
        Config::set('easypay.enabled', true);
        Config::set('easypay.base_url', 'https://api.test.easypay.pt/2.0');

        Http::fake([
            'https://api.test.easypay.pt/2.0/checkout' => Http::response([
                'id' => '5db36b83-0664-4bc4-a760-7040ac3726f0',
                'session' => 'session-token-example',
                'status' => 'created',
            ], 201),

            'https://api.test.easypay.pt/2.0/checkout/5db36b83-0664-4bc4-a760-7040ac3726f0' => Http::response([
                'id' => '5db36b83-0664-4bc4-a760-7040ac3726f0',
                'status' => 'created',
                'order' => ['key' => 'dummy', 'value' => 10.5],
            ], 200),
        ]);

        $user = User::factory()->create(['language' => 'pt']);
        $product = Product::factory()->create(['price' => 10.50, 'stock' => 10]);
        $address = Address::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)->withSession(['cart' => [ $product->id => 1 ]])
            ->post(route('checkout.place'), ['address_id' => $address->id])
            ->assertRedirect(route('orders.index'));

        $order = Order::where('user_id', $user->id)->latest()->first();
        $this->assertNotNull($order);

        $session = \App\Models\EasypayCheckoutSession::where('order_id', $order->id)->first();
        $this->assertNotNull($session);

        $resp = $this->actingAs($user)->getJson(route('orders.pay.checkout_info', ['order' => $order->uuid, 'session' => $session->id]));
        $resp->assertStatus(200)->assertJson(['ok' => true]);
        $this->assertEquals('created', $resp->json('body.status'));
    }

    public function test_fetch_checkout_info_returns_404_when_no_checkout_id()
    {
        Config::set('easypay.enabled', true);

        $user = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $user->id]);
        $session = \App\Models\EasypayCheckoutSession::create(['order_id' => $order->id, 'status' => 'created']);

        $this->actingAs($user)->getJson(route('orders.pay.checkout_info', ['order' => $order->uuid, 'session' => $session->id]))
            ->assertStatus(404)->assertJson(['ok' => false]);
    }
}
