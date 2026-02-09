<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\Config;

class OrdersPayButtonTest extends TestCase
{
    use RefreshDatabase;

    public function test_pay_now_button_hidden_and_message_shown_when_easypay_disabled_on_order_page()
    {
        Config::set('easypay.enabled', false);

        $user = User::factory()->create();
        $product = Product::factory()->create(['price' => 5.0, 'stock' => 5]);
        $order = Order::factory()->for($user)->create([ 'status' => 'WAITING_PAYMENT', 'is_paid' => false ]);

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
}
