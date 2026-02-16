<?php

namespace Tests\Unit;

use App\Models\Order;
use App\Models\Product;
use App\Models\Tax;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_mark_as_paid_by_easypay_queues_customer_email()
    {
        Mail::fake();

        $user = User::factory()->create(['language' => 'en-UK']);
        $order = Order::factory()->for($user)->create(['is_paid' => false, 'status' => 'WAITING_PAYMENT']);

        $ok = $order->markAsPaid('easypay', ['payment_id' => 'p_1']);

        $this->assertTrue($ok);
        $this->assertTrue($order->fresh()->is_paid);

        Mail::assertQueued(\App\Mail\OrderNotification::class, function ($mail) use ($user) {
            return $mail->hasTo($user->email) && ($mail->locale === 'en-UK' || $mail->locale === 'en');
        });
    }

    public function test_order_notification_includes_shipping_item_when_shipping_cost_present()
    {
        Mail::fake();

        $user = User::factory()->create(['language' => 'en-UK']);

        $order = Order::factory()->for($user)->create([
            'shipping_gross' => 5.50,
            'shipping_tier_name' => 'Standard',
            'products_total_gross' => 10.00,
            'total_gross' => 15.50,
        ]);

        $tax = Tax::create(['name' => 'VAT', 'percentage' => 23, 'is_active' => true]);

        $product = Product::create([
            'tax_id' => $tax->id,
            'price' => 10.00,
            'stock' => 10,
            'weight' => 1.0,
            'active' => true,
        ]);

        $order->items()->create([
            'product_id' => $product->id,
            'quantity' => 1,
            'was_backordered' => false,
            'unit_price_gross' => 10.00,
            'tax_percentage' => 0,
            'unit_weight' => 1.0,
            'total_net' => 10.00,
            'total_tax' => 0.00,
            'total_gross' => 10.00,
        ]);

        $m = new \App\Mail\OrderNotification($order, 'orders.email.event.placed', $user->name, ($order->status ?? null), ['status' => ($order->status ?? null)]);
        $rendered = $m->render();

        $this->assertStringContainsString($order->shipping_tier_name, $rendered);
        $this->assertStringContainsString(number_format($order->shipping_gross, 2), $rendered);
        // ensure list is rendered as HTML list (no accidental preformatted/code block)
        $this->assertStringContainsString('<ul', $rendered);
        $this->assertStringNotContainsString('<pre><code>', $rendered);

        // plain-text conversion should contain the shipping line and no raw HTML tags
        $markdown = app(\Illuminate\Mail\Markdown::class);
        $plain = $markdown->renderText('emails.order-notification', [
            'order' => $order,
            'eventLabel' => t('orders.email.event.placed'),
            'recipientName' => $user->name,
            'statusLabel' => $order->status ?? null,
            'actionUrl' => null,
        ]);

        $this->assertStringNotContainsString('<li>', $plain);
        $this->assertStringContainsString($order->shipping_tier_name, $plain);
        $this->assertStringContainsString(number_format($order->shipping_gross, 2), $plain);
    }

    public function test_order_notification_does_not_include_shipping_when_free()
    {
        Mail::fake();

        $user = User::factory()->create(['language' => 'en-UK']);

        $order = Order::factory()->for($user)->create([
            'shipping_gross' => 0.00,
            'shipping_tier_name' => 'Free',
            'products_total_gross' => 10.00,
            'total_gross' => 10.00,
        ]);

        $tax = Tax::create(['name' => 'VAT', 'percentage' => 23, 'is_active' => true]);

        $product = Product::create([
            'tax_id' => $tax->id,
            'price' => 10.00,
            'stock' => 10,
            'weight' => 1.0,
            'active' => true,
        ]);

        $order->items()->create([
            'product_id' => $product->id,
            'quantity' => 1,
            'was_backordered' => false,
            'unit_price_gross' => 10.00,
            'tax_percentage' => 0,
            'unit_weight' => 1.0,
            'total_net' => 10.00,
            'total_tax' => 0.00,
            'total_gross' => 10.00,
        ]);

        $m = new \App\Mail\OrderNotification($order, 'orders.email.event.placed', $user->name, ($order->status ?? null), ['status' => ($order->status ?? null)]);
        $rendered = $m->render();

        $this->assertStringNotContainsString($order->shipping_tier_name, $rendered);
        $this->assertStringNotContainsString('Shipping', $rendered);
        // ensure list is rendered as HTML list (no accidental preformatted/code block)
        $this->assertStringContainsString('<ul', $rendered);
        $this->assertStringNotContainsString('<pre><code>', $rendered);

        // also assert the plain-text conversion does not contain raw HTML tags
        $markdown = app(\Illuminate\Mail\Markdown::class);
        $plain = $markdown->renderText('emails.order-notification', [
            'order' => $order,
            'eventLabel' => t('orders.email.event.placed'),
            'recipientName' => $user->name,
            'statusLabel' => $order->status ?? null,
            'actionUrl' => null,
        ]);

        $this->assertStringNotContainsString('<li>', $plain);
        $this->assertStringNotContainsString($order->shipping_tier_name, $plain);
        // do not assert on raw numeric '0.00' which may appear elsewhere in the text version
    }

    public function test_db_translation_for_orders_email_shipping_label_is_used()
    {
        // insert DB translations and clear translation cache so t() picks them up
        \App\Models\StaticTranslation::create(['key' => 'orders.email.shipping_label', 'locale' => 'en-UK', 'value' => 'Shipping']);
        \App\Models\StaticTranslation::create(['key' => 'orders.email.shipping_label', 'locale' => 'pt-PT', 'value' => 'Envio']);
        \Illuminate\Support\Facades\Cache::forget('static_translations_all');

        app()->setLocale('en-UK');
        $this->assertEquals('Shipping', t('orders.email.shipping_label'));

        app()->setLocale('pt-PT');
        $this->assertEquals('Envio', t('orders.email.shipping_label'));
    }
}
