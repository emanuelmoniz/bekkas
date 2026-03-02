<?php

namespace Tests\Feature;

use App\Models\EasypayCheckoutSession;
use App\Models\EasypayPayment;
use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class EasypayWebhookRefundTest extends TestCase
{
    public function test_refund_success_notification_refreshes_payment_and_marks_order_refunded()
    {
        \Illuminate\Support\Facades\Mail::fake();

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

        // Ensure Easypay client is enabled and base_url is set
        \Illuminate\Support\Facades\Config::set('easypay.enabled', true);
        \Illuminate\Support\Facades\Config::set('easypay.base_url', 'https://api.test.easypay.pt/2.0');

        // Fake Easypay API: refund -> success, single -> refunded
        Http::fake([
            'https://api.test.easypay.pt/2.0/refund/r_1' => Http::response([
                'id' => 'r_1',
                'status' => 'success',
                'value' => 11.00,
                'capture' => [
                    'payment_id' => 'pay_r_1',
                ],
            ], 200),

            'https://api.test.easypay.pt/2.0/single/pay_r_1' => Http::response([
                'id' => 'pay_r_1',
                'payment_status' => 'refunded',
                'captures' => [['id' => 'cap_r_1', 'status' => 'refunded', 'value' => 11.00]],
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
        $this->assertEquals('CANCELED', $order->status);

        \Illuminate\Support\Facades\Mail::assertQueued(\App\Mail\OrderNotification::class, function ($mail) use ($order) {
            $expected = new \App\Mail\OrderNotification($order, 'orders.email.event.refunded', $order->user->name, (t('orders.refunded') ?: 'Refunded'), ['status' => (t('orders.refunded') ?: 'Refunded')]);

            return $mail->subject === $expected->subject && $mail->hasTo($order->user->email);
        });
    }

    public function test_refund_notification_ignored_when_payment_not_paid()
    {
        \Illuminate\Support\Facades\Mail::fake();

        $order = Order::factory()->create();
        $order->is_paid = false;
        $order->save();

        $payment = EasypayPayment::create([
            'payment_id' => 'pay_r_2',
            'payment_status' => 'pending',
            'refund_id' => 'r_2',
            'raw_response' => [],
        ]);

        \Illuminate\Support\Facades\Config::set('easypay.enabled', true);
        \Illuminate\Support\Facades\Config::set('easypay.base_url', 'https://api.test.easypay.pt/2.0');

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

        \Illuminate\Support\Facades\Mail::assertNothingQueued(\App\Mail\OrderNotification::class);
    }

    public function test_refund_does_not_change_status_when_not_processing()
    {
        \Illuminate\Support\Facades\Mail::fake();

        $order = Order::factory()->create(['status' => 'SHIPPED']);
        $order->is_paid = true;
        $order->save();

        $payment = EasypayPayment::create([
            'order_id' => $order->id,
            'payment_id' => 'pay_r_4',
            'payment_status' => 'paid',
            'refund_id' => 'r_4',
            'raw_response' => [],
        ]);

        \Illuminate\Support\Facades\Config::set('easypay.enabled', true);
        \Illuminate\Support\Facades\Config::set('easypay.base_url', 'https://api.test.easypay.pt/2.0');

        Http::fake([
            'https://api.test.easypay.pt/2.0/refund/r_4' => Http::response(['id' => 'r_4', 'status' => 'success', 'capture' => ['payment_id' => 'pay_r_4']], 200),
            'https://api.test.easypay.pt/2.0/single/pay_r_4' => Http::response(['id' => 'pay_r_4', 'payment_status' => 'refunded', 'captures' => [['id' => 'cap_r_4', 'status' => 'refunded', 'value' => 11.00]]], 200),
        ]);

        config()->set('easypay.webhook_user', 'webhook-user');
        config()->set('easypay.webhook_pass', 'webhook-pass');
        config()->set('easypay.webhook_header', 'x-easypay-code');
        config()->set('easypay.webhook_secret', 'shh-4');

        $payload = ['id' => 'r_4', 'type' => 'refund', 'status' => 'success'];

        $resp = $this->withHeaders([
            'PHP_AUTH_USER' => 'webhook-user',
            'PHP_AUTH_PW' => 'webhook-pass',
            'x-easypay-code' => 'shh-4',
            'Content-Type' => 'application/json',
        ])->postJson('/webhooks/easypay', $payload);

        $resp->assertStatus(200)->assertSeeText('OK');

        $payment->refresh();
        $this->assertEquals('refunded', $payment->payment_status);

        $order->refresh();
        $this->assertTrue($order->is_refunded);
        $this->assertEquals('SHIPPED', $order->status);

        \Illuminate\Support\Facades\Mail::assertQueued(\App\Mail\OrderNotification::class);
    }
}
