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

    public function test_easypay_sdk_includes_language_mapping_and_respects_manifest_language()
    {
        $user = User::factory()->create();
        $order = Order::factory()->for($user)->create(['status' => 'WAITING_PAYMENT', 'is_paid' => false]);

        $manifest = [
            'checkout' => ['id' => 'test-checkout-123', 'status' => 'pending'],
            'payment' => ['id' => 'pay-1', 'status' => 'pending'],
            // server-side payload may include customer.language in normalized form (e.g. "PT")
            'customer' => ['language' => 'PT'],
        ];

        $session = \App\Models\EasypayCheckoutSession::create([
            'order_id' => $order->id,
            'checkout_id' => 'test-checkout-123',
            'session_id' => 'sess-1',
            'is_active' => true,
            'status' => 'pending',
            'message' => json_encode($manifest),
        ]);

        Config::set('easypay.base_url', 'https://api.test.easypay.pt/2.0');
        \Illuminate\Support\Facades\Http::fake([
            'https://api.test.easypay.pt/2.0/checkout/test-checkout-123' => \Illuminate\Support\Facades\Http::response($manifest, 200),
        ]);

        $resp = $this->actingAs($user)->get(route('orders.pay', $order->uuid));
        $resp->assertStatus(200);

        // our JS mapping helper and options-builder should be present and wired to use the manifest
        $this->assertStringContainsString('mapEasypayLanguage', $resp->getContent());
        $this->assertStringContainsString('buildEasypayOptions', $resp->getContent());
        $this->assertStringContainsString('buildEasypayOptions(manifest)', $resp->getContent());

        // options must include the loading hint the SDK accepts and set it to true
        $this->assertStringContainsString('showLoading', $resp->getContent());
        $this->assertStringContainsString('showLoading: true', $resp->getContent());

        // the runtime must expose handler globals so the options-builder can attach
        // real functions (avoids passing `undefined` into the SDK).
        $this->assertStringContainsString('window.__easypay_onSuccess', $resp->getContent());
        $this->assertStringContainsString('window.__easypay_onClose', $resp->getContent());
        $this->assertStringContainsString('window.__easypay_onError', $resp->getContent());
        $this->assertStringContainsString('window.__easypay_onPaymentError', $resp->getContent());

        // when the application/user locale is Portuguese the client receives that as a fallback
        // (the SDK language mapping will therefore be able to map it to "pt_PT" at runtime)
        $this->assertStringContainsString('const appLocale =', $resp->getContent());
        $this->assertStringContainsString('const appLocale = '.json_encode(app()->getLocale()), $resp->getContent());
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
