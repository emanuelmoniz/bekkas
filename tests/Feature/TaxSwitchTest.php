<?php

namespace Tests\Feature;

use App\Models\Country;
use App\Models\Product;
use App\Models\ShippingConfig;
use App\Models\ShippingTier;
use App\Models\Tax;
use App\Models\User;
use App\Services\ShippingCalculator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaxSwitchTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Ensure DB translations exist for tests (t() uses DB-driven translations)
        \App\Models\StaticTranslation::create(['key' => 'tax.included_in_price', 'locale' => 'en-UK', 'value' => 'All taxes are included in the price']);
        \App\Models\StaticTranslation::create(['key' => 'tax.included_in_price', 'locale' => 'pt-PT', 'value' => 'Todos os impostos estão incluídos no preço']);
    }

    public function test_cart_and_checkout_show_disclaimer_when_tax_disabled()
    {
        $user = User::factory()->create();

        $tax = Tax::create(['name' => 'VAT', 'percentage' => 23, 'is_active' => true]);

        $product = Product::create([
            'tax_id' => $tax->id,
            'price' => 10.00,
            'stock' => 5,
            'weight' => 1.0,
            'active' => true,
        ]);

        // Simulate tax feature turned off (DB/env resolved to config key at runtime)
        config(['app.tax_enabled' => false]);

        $res = $this->withSession(['cart' => [$product->id => 1]])
            ->actingAs($user)
            ->get(route('cart.index'))
            ->assertStatus(200);

        // t() may return the DB translation (when seeded) or fall back to the key in tests.
        $content = $res->getContent();
        $this->assertTrue(
            str_contains($content, 'All taxes are included in the price')
            || str_contains($content, 'Todos os impostos estão incluídos no preço')
            || str_contains($content, 'tax.included_in_price'),
            "Cart page content did not contain expected disclaimer:\n".$content
        );

        // Prepare address + shipping tier for checkout view
        $country = Country::firstOrCreate(['iso_alpha2' => 'PT'], ['name_pt' => 'Portugal', 'name_en' => 'Portugal', 'country_code' => '351', 'is_active' => true]);
        $address = $user->addresses()->create([
            'title' => 'Home',
            'address_line_1' => 'Rua Teste 1',
            'postal_code' => '1000-000',
            'city' => 'Lisbon',
            'country_id' => $country->id,
            'is_default' => true,
        ]);

        $tier = ShippingTier::create([
            'tax_id' => $tax->id,
            'weight_from' => 0,
            'weight_to' => 9999,
            'cost_gross' => 5.00,
            'shipping_days' => 5,
            'active' => true,
        ]);

        ShippingConfig::set('default_shipping_tier_id', $tier->id);

        $res = $this->withSession(['cart' => [$product->id => 1]])
            ->actingAs($user)
            ->get(route('checkout.index'))
            ->assertStatus(200);

        $content = $res->getContent();
        $this->assertTrue(
            str_contains($content, 'All taxes are included in the price')
            || str_contains($content, 'Todos os impostos estão incluídos no preço')
            || str_contains($content, 'tax.included_in_price')
        );
        $this->assertFalse(str_contains($content, 'Products tax'));
    }

    public function test_place_order_records_zero_taxes_when_tax_disabled()
    {
        $user = User::factory()->create();

        $tax = Tax::create(['name' => 'VAT', 'percentage' => 23, 'is_active' => true]);

        $product = Product::create([
            'tax_id' => $tax->id,
            'price' => 20.00,
            'stock' => 5,
            'weight' => 1.0,
            'active' => true,
            'is_backorder' => false,
        ]);

        $country = Country::firstOrCreate(['iso_alpha2' => 'PT'], ['name_pt' => 'Portugal', 'name_en' => 'Portugal', 'country_code' => '351', 'is_active' => true]);

        $address = $user->addresses()->create([
            'title' => 'Home',
            'address_line_1' => 'Rua Teste 1',
            'postal_code' => '1000-000',
            'city' => 'Lisbon',
            'country_id' => $country->id,
            'is_default' => true,
        ]);

        $tier = ShippingTier::create([
            'tax_id' => $tax->id,
            'weight_from' => 0,
            'weight_to' => 9999,
            'cost_gross' => 5.00,
            'shipping_days' => 5,
            'active' => true,
        ]);

        ShippingConfig::set('default_shipping_tier_id', $tier->id);

        // Disable tax globally
        config(['app.tax_enabled' => false]);

        $response = $this->withSession(['cart' => [$product->id => 2]])
            ->actingAs($user)
            ->post(route('checkout.place'), [
                'address_id' => $address->id,
                'shipping_tier_id' => $tier->id,
            ]);

        $order = \App\Models\Order::where('user_id', $user->id)->latest()->first();
        $this->assertNotNull($order);

        // Taxes must be zero when disabled
        $this->assertEquals(0.00, (float) $order->products_total_tax);
        $this->assertEquals(0.00, (float) $order->shipping_tax);
        $this->assertEquals(0.00, (float) $order->total_tax);

        // Order items should also show zero tax
        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'total_tax' => 0.00,
            'tax_percentage' => 0.00,
        ]);
    }

    public function test_shipping_calculator_respects_tax_toggle()
    {
        $tax = Tax::create(['name' => 'VAT', 'percentage' => 23, 'is_active' => true]);

        $tier = ShippingTier::create([
            'tax_id' => $tax->id,
            'weight_from' => 0,
            'weight_to' => 9999,
            'cost_gross' => 10.00,
            'shipping_days' => 3,
            'active' => true,
        ]);

        // tax enabled -> tax should be computed
        config(['app.tax_enabled' => true]);
        $res = ShippingCalculator::calculate(1.0);
        $this->assertGreaterThan(0, $res['tax']);

        // tax disabled -> tax must be zero
        config(['app.tax_enabled' => false]);
        $res = ShippingCalculator::calculate(1.0);
        $this->assertEquals(0.0, $res['tax']);
    }

    public function test_order_show_uses_order_tax_flag_not_current_config()
    {
        $user = User::factory()->create();

        $tax = Tax::create(['name' => 'VAT', 'percentage' => 23, 'is_active' => true]);

        $product = Product::create([
            'tax_id' => $tax->id,
            'price' => 20.00,
            'stock' => 5,
            'weight' => 1.0,
            'active' => true,
        ]);

        $country = Country::firstOrCreate(['iso_alpha2' => 'PT'], ['name_pt' => 'Portugal', 'name_en' => 'Portugal', 'country_code' => '351', 'is_active' => true]);

        $address = $user->addresses()->create([
            'title' => 'Home',
            'address_line_1' => 'Rua Teste 1',
            'postal_code' => '1000-000',
            'city' => 'Lisbon',
            'country_id' => $country->id,
            'is_default' => true,
        ]);

        $tier = ShippingTier::create([
            'tax_id' => $tax->id,
            'weight_from' => 0,
            'weight_to' => 9999,
            'cost_gross' => 5.00,
            'shipping_days' => 5,
            'active' => true,
        ]);

        ShippingConfig::set('default_shipping_tier_id', $tier->id);

        // Place order with tax DISABLED
        config(['app.tax_enabled' => false]);

        $this->withSession(['cart' => [$product->id => 1]])
            ->actingAs($user)
            ->post(route('checkout.place'), [
                'address_id' => $address->id,
                'shipping_tier_id' => $tier->id,
            ]);

        $order = \App\Models\Order::where('user_id', $user->id)->latest()->first();
        $this->assertNotNull($order);
        $this->assertFalse((bool) $order->tax_enabled, 'Order.tax_enabled should be false for orders placed when tax was disabled');

        // Now enable tax globally (simulate admin toggling tax back on)
        config(['app.tax_enabled' => true]);

        // When viewing the order, the UI must reflect the order-level tax flag (i.e. show the "included" message)
        $res = $this->actingAs($user)->get(route('orders.show', $order));
        $res->assertStatus(200);

        $content = $res->getContent();
        $this->assertTrue(
            str_contains($content, 'All taxes are included in the price')
            || str_contains($content, 'Todos os impostos estão incluídos no preço')
            || str_contains($content, 'tax.included_in_price'),
            "Order page content did not contain expected disclaimer:\n".$content
        );

        // Should NOT show a numeric products tax line (even though app.tax_enabled is now true)
        $this->assertFalse(str_contains($content, 'Products tax'));
    }
}
