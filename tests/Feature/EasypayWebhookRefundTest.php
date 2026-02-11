<?php

namespace Tests\Feature;

use App\Models\EasypayPayment;
use App\Models\EasypayCheckoutSession;
use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class EasypayWebhookRefundTest extends TestCase
{
    public function test_refund_success_notification_refreshes_payment_and_marks_order_refunded()
    {
        $order = Order::factory()->create();
        // order already paid
        $order->is_paid = true;
        $order->save();

        $session = EasypayCheckoutSession::create(['order_id' => $order->id, 'payload_id' => null, 'checkout_id' => 'chk_r_1', 'is_active' => true, 'status' => 'paid']);

        $payment = EasypayPayment::create([
            'order_id' => $order->id,
            'checkout_id' => $session->checkout_id,
            'payment_id' => 'pay_r_1',
            'payment_status' => 'paid',
            'payment_method' => 'card',
            'paid_at' => now(),
            'refund_id' => 'r_1',
            'raw_response' => ['x' => 1],
        ]);

        // Fake Easypay API: refund -> success, single -> refunded
        Http::fake([
            'https://api.test.easypay.pt/2.0/refund/r_1' => Http::response([
                'id' => 'r_1',
                'status' => 'success',
                'value' => 11.00,
                'capture' => [
                    'payment_id' => 'pay_r_1'
                ],
            ], 200),

            'https://api.test.easypay.pt/2.0/single/pay_r_1' => Http::response([
                'id' => 'pay_r_1',
                'payment_status' => 'refunded',
                'captures' => [ ['id' => 'cap_r_1', 'status' => 'refunded', 'value' => 11.00] ],
            ], 200),
        ]);

        // configure webhook auth/header
        config()->set('easypay.webhook_user', 'webhook-user');
        config()->set('easypay.webhook_pass', 'webhook-pass');
        config()->set('easypay.webhook_header', 'x-easypay-code');
        config()->set('easypay.webhook_secret', 'shh-r');

        $payload = ['id' => 'r_1', 'type' => 'refund', 'status' => 'success'];

        $resp = $this->withHeaders([
            'PHP_AUTH_USER' => 'webhook-user',
            'PHP_AUTH_PW' => 'webhook-pass',
            'x-easypay-code' => 'shh-r',
            'Content-Type' => 'application/json',
        ])->postJson('/webhooks/easypay', $payload);

        $resp->assertStatus(200)->assertSeeText('OK');

        $payment->refresh();
        $this->assertEquals('refunded', $payment->payment_status);
        $this->assertEquals('cap_r_1', $payment->capture_id);

        $order->refresh();
        $this->assertTrue($order->is_refunded);
    }

    public function test_refund_notification_ignored_when_payment_not_paid()
    {
        $order = Order::factory()->create();
        $order->is_paid = false;
        $order->save();

        $payment = EasypayPayment::create([
            'payment_id' => 'pay_r_2',
            'payment_status' => 'pending',
            'refund_id' => 'r_2',
            'raw_response' => [],
        ]);

        Http::fake([
            'https://api.test.easypay.pt/2.0/refund/r_2' => Http::response(['id' => 'r_2', 'status' => 'success', 'capture' => ['payment_id' => 'pay_r_2']], 200),
            'https://api.test.easypay.pt/2.0/single/pay_r_2' => Http::response(['id' => 'pay_r_2', 'payment_status' => 'refunded'], 200),
        ]);

        config()->set('easypay.webhook_user', 'webhook-user');
        config()->set('easypay.webhook_pass', 'webhook-pass');
        config()->set('easypay.webhook_header', 'x-easypay-code');
        config()->set('easypay.webhook_secret', 'shh-2');

        $payload = ['id' => 'r_2', 'type' => 'refund', 'status' => 'success'];

        $resp = $this->withHeaders([
            'PHP_AUTH_USER' => 'webhook-user',
            'PHP_AUTH_PW' => 'webhook-pass',
            'x-easypay-code' => 'shh-2',
            'Content-Type' => 'application/json',
        ])->postJson('/webhooks/easypay', $payload);

        $resp->assertStatus(200)->assertSeeText('OK');

        $payment->refresh();
        $this->assertEquals('pending', $payment->payment_status);

        $order->refresh();
        $this->assertFalse($order->is_refunded);
    }

    public function test_refund_marks_payment_status_even_if_single_not_refunded()
    {
        $order = Order::factory()->create();
        $order->is_paid = true;
        $order->save();

        $payment = EasypayPayment::create([
            'order_id' => $order->id,
            'payment_id' => 'pay_r_3',
            'payment_status' => 'paid',
            'refund_id' => 'r_3',
            'raw_response' => [],
        ]);

        Http::fake([
            'https://api.test.easypay.pt/2.0/refund/r_3' => Http::response(['id' => 'r_3', 'status' => 'success', 'capture' => ['payment_id' => 'pay_r_3']], 200),
            // remote single still reports 'paid' for whatever reason — we must still mark local as refunded
            'https://api.test.easypay.pt/2.0/single/pay_r_3' => Http::response(['id' => 'pay_r_3', 'payment_status' => 'paid', 'captures' => [['id' => 'cap_r_3', 'status' => 'success', 'value' => 11.00]]], 200),
        ]);

        config()->set('easypay.webhook_user', 'webhook-user');
        config()->set('easypay.webhook_pass', 'webhook-pass');
        config()->set('easypay.webhook_header', 'x-easypay-code');
        config()->set('easypay.webhook_secret', 'shh-3');

        $payload = ['id' => 'r_3', 'type' => 'refund', 'status' => 'success'];

        $resp = $this->withHeaders([
            'PHP_AUTH_USER' => 'webhook-user',
            'PHP_AUTH_PW' => 'webhook-pass',
            'x-easypay-code' => 'shh-3',
            'Content-Type' => 'application/json',
        ])->postJson('/webhooks/easypay', $payload);

        $resp->assertStatus(200)->assertSeeText('OK');

        $payment->refresh();
        $this->assertEquals('refunded', $payment->payment_status);

        $order->refresh();
        $this->assertTrue($order->is_refunded);
    }
}
