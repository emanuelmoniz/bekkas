<?php

namespace Tests\Feature;

use App\Models\EasypayCheckoutSession;
use App\Models\EasypayPayment;
use App\Models\EasypayPayload;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class EasypaySdkErrorHandlingTest extends TestCase
{
    public function test_prepare_endpoint_returns_already_paid_when_order_and_payment_are_paid()
    {
        Config::set('easypay.enabled', true);

        $user = User::factory()->create();
        $order = Order::factory()->for($user)->create(['is_paid' => true]);

        $payload = EasypayPayload::create(['order_id' => $order->id, 'payload' => ['dummy' => true]]);
        EasypayCheckoutSession::create(['order_id' => $order->id, 'payload_id' => $payload->id, 'is_active' => false]);
        EasypayPayment::create(['order_id' => $order->id, 'payment_id' => 'p_1', 'payment_status' => 'paid']);

        $this->actingAs($user)
            ->postJson("/orders/{$order->uuid}/pay/prepare")
            ->assertStatus(200)
            ->assertJson(['action' => 'already-paid']);
    }

    public function test_prepare_endpoint_cancels_pending_session_and_returns_new_manifest()
    {
        Config::set('easypay.enabled', true);
        Config::set('easypay.base_url', 'https://api.test.easypay.pt/2.0');

        $user = User::factory()->create();
        $order = Order::factory()->for($user)->create(['is_paid' => false]);

        $payload = EasypayPayload::create(['order_id' => $order->id, 'payload' => ['dummy' => true]]);
        $session = EasypayCheckoutSession::create(['order_id' => $order->id, 'payload_id' => $payload->id, 'checkout_id' => 'chk_old', 'is_active' => true]);
        $payment = EasypayPayment::create(['order_id' => $order->id, 'payment_id' => 'pay_old', 'payment_status' => 'pending']);

        // Fake cancel and new /checkout responses
        Http::fake([
            'https://api.test.easypay.pt/2.0/checkout/chk_old' => Http::response(null, 204),
            'https://api.test.easypay.pt/2.0/checkout' => Http::response(['id' => 'chk_new', 'session' => 'sess_new'], 201),
        ]);

        $resp = $this->actingAs($user)->postJson("/orders/{$order->uuid}/pay/prepare");
        $resp->assertStatus(200)->assertJsonPath('action', 'new-manifest');

        $this->assertDatabaseHas('easypay_checkout_sessions', ['checkout_id' => 'chk_old', 'is_active' => false]);
        $this->assertDatabaseHas('easypay_checkout_sessions', ['checkout_id' => 'chk_new', 'is_active' => true]);
    }

    public function test_logSdkError_already_paid_with_pending_payment_updates_payment_and_marks_order_when_paid_remotely()
    {
        Config::set('easypay.enabled', true);
        Config::set('easypay.base_url', 'https://api.test.easypay.pt/2.0');

        $user = User::factory()->create();
        $order = Order::factory()->for($user)->create(['is_paid' => false]);

        $payload = EasypayPayload::create(['order_id' => $order->id, 'payload' => ['dummy' => true]]);
        $session = EasypayCheckoutSession::create(['order_id' => $order->id, 'payload_id' => $payload->id, 'checkout_id' => 'chk_1', 'is_active' => true]);
        $payment = EasypayPayment::create(['order_id' => $order->id, 'payment_id' => 'pay_1', 'payment_status' => 'pending']);

        // Fake single payment to return paid
        Http::fake([
            'https://api.test.easypay.pt/2.0/single/pay_1' => Http::response(['id' => 'pay_1', 'payment_status' => 'paid', 'paid_at' => now()->toIso8601String()], 200),
        ]);

        $resp = $this->actingAs($user)->postJson('/easypay/sdk/error', ['error' => ['code' => 'already-paid', 'payment' => ['id' => 'pay_1'], 'checkoutId' => 'chk_1']]);
        $resp->assertStatus(200)->assertJson(['action' => 'already-paid']);

        $this->assertDatabaseHas('easypay_payments', ['payment_id' => 'pay_1', 'payment_status' => 'paid']);
        $this->assertDatabaseHas('orders', ['id' => $order->id, 'is_paid' => true]);
    }
}
