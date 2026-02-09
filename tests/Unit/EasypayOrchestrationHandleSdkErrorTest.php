<?php

namespace Tests\Unit;

use App\Models\EasypayPayload;
use App\Models\EasypayPayment;
use App\Models\EasypayCheckoutSession;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EasypayOrchestrationHandleSdkErrorTest extends TestCase
{
    use RefreshDatabase;

    public function test_handleSdkError_updates_pending_payment_when_remote_reports_paid()
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

        $orch = new \App\Services\EasypayOrchestrationService();
        $res = $orch->handleSdkError($order, ['code' => 'already-paid', 'payment' => ['id' => 'pay_1'], 'checkoutId' => 'chk_1']);

        $this->assertEquals('already-paid', $res['action']);
        $this->assertDatabaseHas('easypay_payments', ['payment_id' => 'pay_1', 'payment_status' => 'paid']);
        $this->assertDatabaseHas('orders', ['id' => $order->id, 'is_paid' => true]);
    }
}
