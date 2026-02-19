<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class OrdersPayButtonTest extends TestCase
{
    use RefreshDatabase;

    public function test_pay_now_button_hidden_and_message_shown_when_easypay_disabled_on_order_page()
    {
        Config::set('easypay.enabled', false);

        $user = User::factory()->create();
        $product = Product::factory()->create(['price' => 5.0, 'stock' => 5]);
        $order = Order::factory()->for($user)->create(['status' => 'WAITING_PAYMENT', 'is_paid' => false]);

        $resp = $this->actingAs($user)->get(route('orders.show', $order));
        $resp->assertStatus(200);

        // Pay button must not be present
        $resp->assertDontSee(t('orders.pay_now') ?: 'Pay now', false);

        // Informative message should be shown
        $resp->assertSee(t('checkout.gateways.disabled') ?: 'Payment gateways are temporarily disabled', false);

        // Visiting the order page should not create any Easypay rows for this order (avoid global-count flakiness)
        $this->assertDatabaseMissing('easypay_payloads', ['order_id' => $order->id]);
        $this->assertDatabaseMissing('easypay_checkout_sessions', ['order_id' => $order->id]);
    }

    public function test_order_page_shows_payment_info_and_hides_top_pay_button_for_pending_payment()
    {
        Config::set('easypay.enabled', true);

        $user = User::factory()->create();
        $order = Order::factory()->for($user)->create(['status' => 'WAITING_PAYMENT', 'is_paid' => false]);

        \App\Models\EasypayPayment::create([
            'payment_id' => 'pay_mb_1',
            'order_id' => $order->id,
            'payment_status' => 'pending',
            'mb_entity' => '111',
            'mb_reference' => '222',
            'mb_expiration_time' => now()->addDay(),
        ]);

        $resp = $this->actingAs($user)->get(route('orders.show', $order));
        $resp->assertStatus(200);

        // Top-level pay button must be hidden when DB shows pending
        $resp->assertDontSee(t('orders.pay_now') ?: 'Pay now', false);

        // Payment information block must be visible (MB details)
        $resp->assertSee(t('checkout.pay.payment_info_title') ?: 'Payment information', false);
        $resp->assertSee('111', false);
        $resp->assertSee('222', false);

        // There must be a link to the pay page to allow changing/completing the payment
        $resp->assertSee(t('orders.change_payment') ?: 'Change payment', false);
        $this->assertStringContainsString(route('orders.pay', $order->uuid), $resp->getContent());
    }

    public function test_order_page_hides_top_pay_button_for_authorised_payment()
    {
        Config::set('easypay.enabled', true);

        $user = User::factory()->create();
        $order = Order::factory()->for($user)->create(['status' => 'WAITING_PAYMENT', 'is_paid' => false]);

        \App\Models\EasypayPayment::create([
            'payment_id' => 'pay_auth_1',
            'order_id' => $order->id,
            'payment_status' => 'authorised',
        ]);

        $resp = $this->actingAs($user)->get(route('orders.show', $order));
        $resp->assertStatus(200);

        // Top-level pay button must be hidden when DB shows authorised
        $resp->assertDontSee(t('orders.pay_now') ?: 'Pay now', false);

        // Show informative authorised message (server-driven)
        $resp->assertSee(t('checkout.pay.status.authorised') ?: 'Payment authorised — processing is underway, please check your order details in a moment.', false);
    }
}
