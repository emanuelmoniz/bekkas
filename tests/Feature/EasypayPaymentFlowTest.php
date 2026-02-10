<?php

namespace Tests\Feature;

use App\Models\EasypayCheckoutSession;
use App\Models\EasypayPayload;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class EasypayPaymentFlowTest extends TestCase
{
    public function test_on_success_creates_payment_and_marks_order_paid()
    {
        // Fake Easypay single payment response
        $paymentId = 'pay_test_1';
        Http::fake([
            '*' => Http::response([
                'id' => $paymentId,
                'payment_status' => 'paid',
                'method' => ['type' => 'mbw'],
                'method' => ['card_type' => 'VISA'],
            ], 200)
        ]);

        $user = User::factory()->create();
        $this->actingAs($user);

        $order = Order::factory()->create(['user_id' => $user->id, 'status' => 'WAITING_PAYMENT', 'is_paid' => false]);

        $payload = EasypayPayload::create(['order_id' => $order->id, 'payload' => ['dummy' => true]]);
        $session = EasypayCheckoutSession::create(['order_id' => $order->id, 'payload_id' => $payload->id, 'checkout_id' => 'chk_1', 'session_id' => 'sess', 'is_active' => true]);

        $url = "/orders/{$order->uuid}/pay/verify";

        $resp = $this->postJson($url, ['checkout' => ['id' => 'chk_1', 'payment' => ['id' => $paymentId]]]);

        $resp->assertStatus(201);

        $this->assertDatabaseHas('easypay_payments', ['payment_id' => $paymentId]);

        $order->refresh();
        $this->assertTrue($order->is_paid);
        $this->assertEquals('PROCESSING', $order->status);
    }

    public function test_on_success_persists_client_payload_when_remote_unavailable()
    {
        // Simulate remote Easypay being unavailable — SDK should still persist the payment
        Http::fake([
            '*' => Http::response(null, 502),
        ]);

        $paymentId = 'pay_test_unreachable';

        $user = User::factory()->create();
        $this->actingAs($user);

        $order = Order::factory()->create(['user_id' => $user->id, 'status' => 'WAITING_PAYMENT', 'is_paid' => false]);

        $payload = EasypayPayload::create(['order_id' => $order->id, 'payload' => ['dummy' => true]]);
        $session = EasypayCheckoutSession::create(['order_id' => $order->id, 'payload_id' => $payload->id, 'checkout_id' => 'chk_1', 'session_id' => 'sess', 'is_active' => true]);

        $resp = $this->postJson("/orders/{$order->uuid}/pay/verify", ['checkout' => ['id' => 'chk_1', 'payment' => ['id' => $paymentId, 'status' => 'pending']]]);

        // Controller must persist the SDK-provided payment even if remote fails
        $resp->assertStatus(201);
        $this->assertDatabaseHas('easypay_payments', ['payment_id' => $paymentId, 'payment_status' => 'pending']);

        // Order must NOT be marked paid because authoritative remote was not available
        $order->refresh();
        $this->assertFalse($order->is_paid);
    }
}
