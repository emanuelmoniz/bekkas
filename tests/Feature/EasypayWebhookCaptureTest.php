<?php

namespace Tests\Feature;

use App\Models\EasypayCheckoutSession;
use App\Models\EasypayPayment;
use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class EasypayWebhookCaptureTest extends TestCase
{
    public function test_capture_success_notification_refreshes_payment_and_marks_order_paid_and_updates_checkout()
    {
        // Arrange: create order + checkout session + pending payment
        $order = Order::factory()->create();
        $session = EasypayCheckoutSession::create(['order_id' => $order->id, 'payload_id' => null, 'checkout_id' => 'chk_web_1', 'is_active' => true, 'status' => 'pending']);
        $payment = EasypayPayment::create(['order_id' => $order->id, 'checkout_id' => $session->checkout_id, 'payment_id' => 'pay_web_1', 'payment_status' => 'pending']);

        // Ensure Easypay client is enabled and base_url is correct
        \Illuminate\Support\Facades\Config::set('easypay.enabled', true);
        \Illuminate\Support\Facades\Config::set('easypay.base_url', 'https://api.test.easypay.pt/2.0');

        // Fake Easypay API: single -> paid, checkout -> paid body
        Http::fake([
            'https://api.test.easypay.pt/2.0/single/pay_web_1' => Http::response([
                'id' => 'pay_web_1',
                'payment_status' => 'paid',
                'paid_at' => now()->toIso8601String(),
                'checkout' => ['id' => 'chk_web_1'],
                'captures' => [
                    ['id' => 'cap_1', 'status' => 'success', 'value' => 11],
                ],
            ], 200),
            'https://api.test.easypay.pt/2.0/checkout/chk_web_1' => Http::response(['checkout' => ['id' => 'chk_web_1', 'status' => 'paid']], 200),
        ]);

        // Configure webhook auth/header
        config()->set('easypay.webhook_user', 'webhook-user');
        config()->set('easypay.webhook_pass', 'webhook-pass');
        config()->set('easypay.webhook_header', 'x-easypay-code');
        config()->set('easypay.webhook_secret', 'shh');

        $payload = ['id' => 'pay_web_1', 'type' => 'capture', 'status' => 'success'];

        // Ensure mails are captured
        \Illuminate\Support\Facades\Mail::fake();

        // Ensure the order's user has an explicit language so we can assert it is respected
        $order->user->update(['language' => 'en-UK']);

        // Sanity-check: EasypayService fakes must return expected values before webhook handler runs
        $svcSingle = (new \App\Services\EasypayService)->getSinglePayment('pay_web_1');
        $this->assertIsArray($svcSingle);
        $this->assertEquals('paid', $svcSingle['payment_status']);

        $svcCheckout = \App\Services\EasypayService::fetchCheckout('chk_web_1');
        $this->assertTrue($svcCheckout['ok']);
        $this->assertIsArray($svcCheckout['body']);

        // Act
        $resp = $this->withHeaders([
            'PHP_AUTH_USER' => 'webhook-user',
            'PHP_AUTH_PW' => 'webhook-pass',
            'x-easypay-code' => 'shh',
            'Content-Type' => 'application/json',
        ])->postJson('/webhooks/easypay', $payload);

        // Assert
        $resp->assertStatus(200)->assertSeeText('OK');

        $payment->refresh();
        $this->assertEquals('paid', $payment->payment_status);
        $this->assertNotNull($payment->paid_at);

        // capture_id must be persisted when captures are present in the remote response
        $this->assertEquals('cap_1', $payment->capture_id);

        $this->assertTrue(Order::find($order->id)->is_paid);

        $session->refresh();
        $this->assertStringContainsString('chk_web_1', $session->message);
        $this->assertEquals('paid', $session->status);

    }
}
