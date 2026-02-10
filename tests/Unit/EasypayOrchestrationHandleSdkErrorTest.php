<?php

namespace Tests\Unit;

use App\Models\EasypayCheckoutSession;
use App\Models\EasypayPayload;
use App\Models\EasypayPayment;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class EasypayOrchestrationHandleSdkErrorTest extends TestCase
{
    use RefreshDatabase;

    public function test_handle_sdk_error_updates_pending_payment_when_remote_reports_paid()
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
        ]);

        $orch = new \App\Services\EasypayOrchestrationService;
        $res = $orch->handleSdkError($order, ['code' => 'already-paid', 'payment' => ['id' => 'pay_1'], 'checkoutId' => 'chk_1']);

        $this->assertEquals('already-paid', $res['action']);
        $this->assertDatabaseHas('easypay_payments', ['payment_id' => 'pay_1', 'payment_status' => 'paid']);
        $this->assertDatabaseHas('orders', ['id' => $order->id, 'is_paid' => true]);
    }

    public function test_handle_sdk_error_does_not_mark_order_paid_when_remote_reports_not_paid_even_if_local_row_is_stale_paid()
    {
        Config::set('easypay.enabled', true);
        Config::set('easypay.base_url', 'https://api.test.easypay.pt/2.0');

        $user = User::factory()->create();
        $order = Order::factory()->for($user)->create(['is_paid' => false]);

        $payload = EasypayPayload::create(['order_id' => $order->id, 'payload' => ['dummy' => true]]);
        EasypayCheckoutSession::create(['order_id' => $order->id, 'payload_id' => $payload->id, 'checkout_id' => 'chk_2', 'is_active' => true]);
        // local (stale) row incorrectly shows paid
        EasypayPayment::create(['order_id' => $order->id, 'payment_id' => 'pay_2', 'payment_status' => 'paid']);

        // remote authoritative endpoint reports pending
        Http::fake([
            'https://api.test.easypay.pt/2.0/single/pay_2' => Http::response(['id' => 'pay_2', 'payment_status' => 'pending'], 200),
        ]);

        $orch = new \App\Services\EasypayOrchestrationService;
        $res = $orch->handleSdkError($order, ['code' => 'already-paid', 'payment' => ['id' => 'pay_2'], 'checkoutId' => 'chk_2']);

        $this->assertNotEquals('already-paid', $res['action']);
        $this->assertDatabaseHas('easypay_payments', ['payment_id' => 'pay_2', 'payment_status' => 'pending']);
        $this->assertDatabaseHas('orders', ['id' => $order->id, 'is_paid' => false]);
    }

    public function test_handle_sdk_error_cancels_all_sessions_and_refreshes_all_payments_on_generic_error()
    {
        Config::set('easypay.enabled', true);
        Config::set('easypay.base_url', 'https://api.test.easypay.pt/2.0');

        $user = User::factory()->create();
        $order = Order::factory()->for($user)->create(['is_paid' => false]);

        $payload = EasypayPayload::create(['order_id' => $order->id, 'payload' => ['dummy' => true]]);

        // two active sessions that should both be cancelled
        EasypayCheckoutSession::create(['order_id' => $order->id, 'payload_id' => $payload->id, 'checkout_id' => 'chk_a', 'is_active' => true]);
        EasypayCheckoutSession::create(['order_id' => $order->id, 'payload_id' => $payload->id, 'checkout_id' => 'chk_b', 'is_active' => true]);

        // two payments: one remains pending, the other remote reports paid
        EasypayPayment::create(['order_id' => $order->id, 'payment_id' => 'pay_x', 'payment_status' => 'pending']);
        EasypayPayment::create(['order_id' => $order->id, 'payment_id' => 'pay_y', 'payment_status' => 'pending']);

        Http::fake([
            'https://api.test.easypay.pt/2.0/single/pay_x' => Http::response(['id' => 'pay_x', 'payment_status' => 'pending'], 200),
            'https://api.test.easypay.pt/2.0/single/pay_y' => Http::response(['id' => 'pay_y', 'payment_status' => 'paid', 'paid_at' => now()->toIso8601String()], 200),

            // When orchestration fetches the checkout details after cancelling, return a body and ensure it's persisted
            'https://api.test.easypay.pt/2.0/checkout/chk_a' => Http::response(['status' => 'success', 'checkout' => ['id' => 'chk_a', 'status' => 'success'], 'payment' => ['methods' => ['mb'], 'type' => 'single'], 'value' => 12.5], 200),
            'https://api.test.easypay.pt/2.0/checkout/chk_b' => Http::response(['status' => 'success', 'checkout' => ['id' => 'chk_b', 'status' => 'success'], 'payment' => ['methods' => ['cc'], 'type' => 'single'], 'value' => 7.5], 200),

            'https://api.test.easypay.pt/2.0/checkout' => Http::response(['id' => 'chk_new', 'session' => 'sess', 'config' => null], 201),
        ]);

        $orch = new \App\Services\EasypayOrchestrationService;
        $res = $orch->handleSdkError($order, ['code' => 'generic-error', 'checkoutId' => 'chk_a']);

        // one of the remote payments was authoritative -> order should be marked paid
        $this->assertEquals('already-paid', $res['action']);
        $this->assertDatabaseHas('easypay_payments', ['payment_id' => 'pay_y', 'payment_status' => 'paid']);
        $this->assertDatabaseHas('orders', ['id' => $order->id, 'is_paid' => true]);

        // both previous sessions must be marked inactive/canceled (local cancel is authoritative)
        $this->assertDatabaseHas('easypay_checkout_sessions', ['checkout_id' => 'chk_a', 'is_active' => false, 'status' => 'canceled']);
        $this->assertDatabaseHas('easypay_checkout_sessions', ['checkout_id' => 'chk_b', 'is_active' => false, 'status' => 'canceled']);

        // fetchCheckout response should have been persisted into session.message (remote status stored there)
        $a = \App\Models\EasypayCheckoutSession::where('checkout_id', 'chk_a')->first();
        $b = \App\Models\EasypayCheckoutSession::where('checkout_id', 'chk_b')->first();
        $this->assertNotNull($a->message);
        $this->assertStringContainsString('"checkout"', $a->message);
        $this->assertStringContainsString('"status"', $a->message);
        $this->assertNotNull($b->message);
        $this->assertStringContainsString('"checkout"', $b->message);
        $this->assertStringContainsString('"status"', $b->message);
    }
}
