<?php

namespace Tests\Feature;

use App\Models\EasypayCheckoutSession;
use App\Models\EasypayPayload;
use App\Models\EasypayPayment;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CheckoutSdkOnErrorTest extends TestCase
{
    use RefreshDatabase;

    public function test_sdk_error_endpoint_cancels_all_sessions_and_returns_new_manifest()
    {
        Config::set('easypay.enabled', true);
        Config::set('easypay.base_url', 'https://api.test.easypay.pt/2.0');

        $user = User::factory()->create();
        $order = Order::factory()->for($user)->create(['is_paid' => false]);

        $payload = EasypayPayload::create(['order_id' => $order->id, 'payload' => ['dummy' => true]]);
        EasypayCheckoutSession::create(['order_id' => $order->id, 'payload_id' => $payload->id, 'checkout_id' => 'chk_a', 'is_active' => true]);
        EasypayCheckoutSession::create(['order_id' => $order->id, 'payload_id' => $payload->id, 'checkout_id' => 'chk_b', 'is_active' => true]);

        EasypayPayment::create(['order_id' => $order->id, 'payment_id' => 'pay_x', 'payment_status' => 'pending']);

        Http::fake([
            'https://api.test.easypay.pt/2.0/checkout/chk_a' => Http::response(['status' => 'success', 'checkout' => ['id' => 'chk_a', 'status' => 'success'], 'payment' => ['methods' => ['mb'], 'type' => 'single'], 'value' => 12.5], 200),
            'https://api.test.easypay.pt/2.0/checkout/chk_b' => Http::response(['status' => 'success', 'checkout' => ['id' => 'chk_b', 'status' => 'success'], 'payment' => ['methods' => ['cc'], 'type' => 'single'], 'value' => 7.5], 200),
            'https://api.test.easypay.pt/2.0/single/pay_x' => Http::response(['id' => 'pay_x', 'payment_status' => 'pending'], 200),
            'https://api.test.easypay.pt/2.0/checkout' => Http::response(['id' => 'chk_new', 'session' => 'sess', 'config' => null], 201),
        ]);

        $this->actingAs($user)
            ->postJson("/orders/{$order->uuid}/pay/sdk-error", ['error' => ['code' => 'generic-error', 'checkoutId' => 'chk_a']])
            ->assertJson(fn ($j) => $j->where('action', 'new-manifest')->has('manifest'));

        $this->assertDatabaseHas('easypay_checkout_sessions', ['checkout_id' => 'chk_a', 'is_active' => false, 'status' => 'canceled']);
        $this->assertDatabaseHas('easypay_checkout_sessions', ['checkout_id' => 'chk_b', 'is_active' => false, 'status' => 'canceled']);
        $this->assertDatabaseHas('easypay_checkout_sessions', ['checkout_id' => 'chk_new']);

        $a = \App\Models\EasypayCheckoutSession::where('checkout_id', 'chk_a')->first();
        $b = \App\Models\EasypayCheckoutSession::where('checkout_id', 'chk_b')->first();
        $this->assertNotNull($a->message);
        $this->assertStringContainsString('"checkout"', $a->message);
        $this->assertStringContainsString('"status"', $a->message);
        $this->assertNotNull($b->message);
        $this->assertStringContainsString('"checkout"', $b->message);
        $this->assertStringContainsString('"status"', $b->message);
    }

    public function test_sdk_error_endpoint_returns_already_paid_when_any_payment_is_paid()
    {
        Config::set('easypay.enabled', true);
        Config::set('easypay.base_url', 'https://api.test.easypay.pt/2.0');

        $user = User::factory()->create();
        $order = Order::factory()->for($user)->create(['is_paid' => false]);

        $payload = EasypayPayload::create(['order_id' => $order->id, 'payload' => ['dummy' => true]]);
        EasypayCheckoutSession::create(['order_id' => $order->id, 'payload_id' => $payload->id, 'checkout_id' => 'chk_1', 'is_active' => true]);
        EasypayPayment::create(['order_id' => $order->id, 'payment_id' => 'pay_1', 'payment_status' => 'pending']);

        Http::fake([
            'https://api.test.easypay.pt/2.0/single/pay_1' => Http::response(['id' => 'pay_1', 'payment_status' => 'paid', 'paid_at' => now()->toIso8601String()], 200),
            'https://api.test.easypay.pt/2.0/checkout/chk_1' => Http::response(['checkout' => ['id' => 'chk_1'], 'payment' => ['methods' => ['cc'], 'type' => 'single'], 'value' => 29.99], 200),
        ]);

        $this->actingAs($user)
            ->postJson("/orders/{$order->uuid}/pay/sdk-error", ['error' => ['code' => 'payment-failure', 'checkoutId' => 'chk_1', 'payment' => ['id' => 'pay_1']]])
            ->assertJson(['action' => 'already-paid']);

        $this->assertDatabaseHas('easypay_payments', ['payment_id' => 'pay_1', 'payment_status' => 'paid']);
        $this->assertDatabaseHas('orders', ['id' => $order->id, 'is_paid' => true]);

        $s = \App\Models\EasypayCheckoutSession::where('checkout_id', 'chk_1')->first();
        $this->assertNotNull($s->message);
        $this->assertStringContainsString('"checkout"', $s->message);
    }
}
