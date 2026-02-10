<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class CheckoutPayPageSdkTest extends TestCase
{
    use RefreshDatabase;

    public function test_pay_page_renders_sdk_placeholder_and_persisted_manifest_for_active_pending_session()
    {
        // Arrange: create user + order in WAITING_PAYMENT
        $user = User::factory()->create();
        $order = Order::factory()->for($user)->create(['status' => 'WAITING_PAYMENT', 'is_paid' => false]);

        // create a persisted checkout session (active + pending). The SDK manifest must be
        // produced from DB fields (`checkout_id`/`session_id`) and has a strict shape.
        $manifest = [
            'checkout' => ['id' => 'test-checkout-123', 'status' => 'pending'],
            'payment' => ['id' => 'pay-1', 'status' => 'pending'],
        ];

        $session = \App\Models\EasypayCheckoutSession::create([
            'order_id' => $order->id,
            'checkout_id' => 'test-checkout-123',
            'session_id' => 'sess-1',
            'is_active' => true,
            'status' => 'pending',
            'message' => json_encode($manifest),
        ]);

        $expectedCanonical = ['id' => 'test-checkout-123', 'session' => 'sess-1', 'config' => null];

        // Ensure server-side verification (fetchCheckout) succeeds so the manifest is exposed
        \Illuminate\Support\Facades\Config::set('easypay.base_url', 'https://api.test.easypay.pt/2.0');
        \Illuminate\Support\Facades\Http::fake([
            'https://api.test.easypay.pt/2.0/checkout/test-checkout-123' => \Illuminate\Support\Facades\Http::response($manifest, 200),
        ]);

        // Act: visit the pay page as the order owner
        $resp = $this->actingAs($user)->get(route('orders.pay', $order->uuid));

        // Assert: page contains the inline widget root, the easypay-manifest and the SDK URL from env
        $resp->assertStatus(200);
        $resp->assertSee('id="easypay-checkout"', false);
        $resp->assertSee('id="easypay-manifest"', false);
        $this->assertStringContainsString(env('EASYPAY_SDK_URL'), $resp->getContent());

        // manifest should be present in the HTML (server-embedded) and be the canonical shape
        $resp->assertSee(json_encode($expectedCanonical), false);

        // IMPORTANT: when a valid manifest/session exists the user must NOT see the unavailable message
        $resp->assertDontSee('Payment system is temporarily unavailable', false);

        // when running in the test env the SDK initialiser should receive testing: true
        $this->assertStringContainsString('const testing = true', $resp->getContent());
    }

    public function test_pay_page_shows_unavailable_and_hides_sdk_when_easypay_disabled()
    {
        Config::set('easypay.enabled', false);

        $user = User::factory()->create();
        $order = Order::factory()->for($user)->create(['status' => 'WAITING_PAYMENT', 'is_paid' => false]);

        $resp = $this->actingAs($user)->get(route('orders.pay', $order->uuid));

        $resp->assertStatus(200);

        $resp->assertStatus(200);
        $resp->assertSee(t('checkout.pay.unavailable') ?: 'Payment system is temporarily unavailable', false);

        // SDK section must not be rendered
        $resp->assertDontSee('id="easypay-checkout"', false);
        $resp->assertDontSee('id="easypay-manifest"', false);

        // Back to order link must still be present
        $resp->assertSee(route('orders.show', $order->uuid));
    }
}
