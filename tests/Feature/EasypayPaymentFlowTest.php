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
            ], 200),
        ]);

        $user = User::factory()->create();
        $this->actingAs($user);

        $order = Order::factory()->create(['user_id' => $user->id, 'status' => 'WAITING_PAYMENT', 'is_paid' => false]);

        $payload = EasypayPayload::create(['order_id' => $order->id, 'payload' => ['dummy' => true]]);
        $session = EasypayCheckoutSession::create(['order_id' => $order->id, 'payload_id' => $payload->id, 'checkout_id' => 'chk_1', 'session_id' => 'sess', 'is_active' => true]);

        $url = "/orders/{$order->uuid}/pay/verify";

        $resp = $this->postJson($url, ['checkout' => ['id' => 'chk_1', 'payment' => ['id' => $paymentId]]]);

        $resp->assertStatus(201);
        $resp->assertJsonPath('payment.payment_id', $paymentId);
        $resp->assertJsonPath('message', t('checkout.pay.success') ?: 'Payment received — thank you. Updating order status…');
        $resp->assertJsonPath('type', 'success');

        // onSuccess should also set the session flash so the global flash UI shows the message
        $resp->assertSessionHas('success', t('checkout.pay.success') ?: 'Payment received — thank you. Updating order status…');
        $resp->assertSessionHas('flash_type', 'success');

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
        $resp->assertJsonPath('payment.payment_id', $paymentId);
        $resp->assertJsonPath('message', t('checkout.pay.on_success.pending') ?: 'Payment info created. Please follow the provided instructions to complete payment; your order will be processed afterwards.');

        // Non-authoritative/informational messages must NOT persist to the session
        $resp->assertSessionMissing('success');

        $this->assertDatabaseHas('easypay_payments', ['payment_id' => $paymentId, 'payment_status' => 'pending']);

        // Order must NOT be marked paid because authoritative remote was not available
        $order->refresh();
        $this->assertFalse($order->is_paid);
    }

    public function test_on_success_authorised_returns_message_and_does_not_mark_paid()
    {
        Http::fake(['*' => Http::response(['id' => 'pay_auth_1', 'payment_status' => 'authorised'], 200)]);

        $user = User::factory()->create();
        $this->actingAs($user);

        $order = Order::factory()->create(['user_id' => $user->id, 'status' => 'WAITING_PAYMENT', 'is_paid' => false]);

        $payload = EasypayPayload::create(['order_id' => $order->id, 'payload' => ['dummy' => true]]);
        $session = EasypayCheckoutSession::create(['order_id' => $order->id, 'payload_id' => $payload->id, 'checkout_id' => 'chk_1', 'session_id' => 'sess', 'is_active' => true]);

        $resp = $this->postJson("/orders/{$order->uuid}/pay/verify", ['checkout' => ['id' => 'chk_1', 'payment' => ['id' => 'pay_auth_1']]]);

        $resp->assertStatus(201);
        $resp->assertJsonPath('payment.payment_id', 'pay_auth_1');
        $resp->assertJsonPath('message', t('checkout.pay.status.authorised') ?: 'Payment authorised — processing is underway, please check your order details in a moment.');

        // Authorised (non-paid) messages are informational and should not persist to session
        $resp->assertSessionMissing('success');

        $this->assertDatabaseHas('easypay_payments', ['payment_id' => 'pay_auth_1', 'payment_status' => 'authorised']);
        $this->assertFalse($order->fresh()->is_paid);
    }
}
