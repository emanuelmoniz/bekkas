<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Order;
use App\Models\EasypayPayload;
use App\Models\EasypayCheckoutSession;

class CheckoutPayPageSdkTest extends TestCase
{
    use RefreshDatabase;

    public function test_pay_page_renders_sdk_placeholder_and_persisted_manifest_for_active_pending_session()
    {
        // Arrange: create user + order in WAITING_PAYMENT
        $user = User::factory()->create();
        $order = Order::factory()->for($user)->create([ 'status' => 'WAITING_PAYMENT', 'is_paid' => false ]);

        // create a persisted checkout session (active + pending) with a sample manifest
        $manifest = [
            'checkout' => ['id' => 'test-checkout-123', 'status' => 'pending'],
            'payment' => ['id' => 'pay-1', 'status' => 'pending']
        ];

        $session = \App\Models\EasypayCheckoutSession::create([
            'order_id' => $order->id,
            'checkout_id' => 'test-checkout-123',
            'session_id' => 'sess-1',
            'is_active' => true,
            'status' => 'pending',
            'message' => json_encode($manifest),
        ]);

        // Act: visit the pay page as the order owner
        $resp = $this->actingAs($user)->get(route('orders.pay', $order->uuid));

        // Assert: page contains the inline widget root, the easypay-manifest and the SDK URL from env
        $resp->assertStatus(200);
        $resp->assertSee('id="easypay-checkout"', false);
        $resp->assertSee('id="easypay-manifest"', false);
        $this->assertStringContainsString(env('EASYPAY_SDK_URL'), $resp->getContent());

        // manifest should be present in the HTML (server-embedded)
        $resp->assertSee(json_encode($manifest), false);

        // when running in the test env the SDK initialiser should receive testing: true
        $this->assertStringContainsString('const testing = true', $resp->getContent());
    }
}
