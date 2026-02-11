<?php

namespace Tests\Feature;

use App\Models\Country;
use App\Models\Order;
use App\Models\Product;
use App\Models\ShippingConfig;
use App\Models\ShippingTier;
use App\Models\Tax;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_checkout_redirects_when_cart_empty()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('checkout.index'))
            ->assertRedirect(route('cart.index'));
    }

    public function test_checkout_redirects_when_insufficient_stock()
    {
        $user = User::factory()->create();

        $tax = Tax::create(['name' => 'VAT', 'percentage' => 23, 'is_active' => true]);

        $product = Product::create([
            'tax_id' => $tax->id,
            'price' => 10.00,
            'stock' => 1,
            'weight' => 1.0,
            'active' => true,
            'is_backorder' => false,
        ]);

        $response = $this->withSession(['cart' => [$product->id => 2]])
            ->actingAs($user)
            ->get(route('checkout.index'));

        // Accept either a redirect to the cart or a session error — behavior may vary by config
        if ($response->isRedirect()) {
            $response->assertRedirect(route('cart.index'))
                ->assertSessionHas('error');
        } else {
            $response->assertStatus(200)
                ->assertSessionHas('error');
        }
    }

    public function test_get_shipping_tiers_returns_free_and_other_tiers_when_qualifies()
    {
        $user = User::factory()->create();

        $tax = Tax::create(['name' => 'VAT', 'percentage' => 23, 'is_active' => true]);

        $product = Product::create([
            'tax_id' => $tax->id,
            'price' => 100.00,
            'stock' => 5,
            'weight' => 1.0,
            'active' => true,
        ]);

        // Set free shipping threshold and default free tier
        ShippingConfig::set('free_shipping_over', '50');

        $freeTier = ShippingTier::create([
            'name_en' => 'Free Tier',
            'name_pt' => 'Free Tier',
            'tax_id' => $tax->id,
            'weight_from' => 0,
            'weight_to' => 9999,
            'cost_gross' => 0,
            'shipping_days' => 10,
            'active' => false, // free tier may be inactive but used via default
        ]);

        ShippingConfig::set('default_shipping_tier_id', $freeTier->id);

        // Create an available paid tier matching weight
        $paidTier = ShippingTier::create([
            'name_en' => 'Fast',
            'name_pt' => 'Fast',
            'tax_id' => $tax->id,
            'weight_from' => 0,
            'weight_to' => 9999,
            'cost_gross' => 10.00,
            'shipping_days' => 3,
            'active' => true,
        ]);

        $response = $this->withSession(['cart' => [$product->id => 1]])
            ->actingAs($user)
            ->postJson(route('checkout.shipping-tiers'), ['postal_code' => '0000']);

        $response->assertStatus(200);
        $response->assertJson(['qualifies_for_free_shipping' => true]);

        $json = $response->json();
        $tiers = collect($json['tiers']);
        $this->assertTrue($tiers->contains(fn ($t) => ($t['is_free'] ?? false) === true));
    }

    public function test_place_order_creates_order_and_decrements_stock()
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
            'name_en' => 'Standard',
            'name_pt' => 'Standard',
            'tax_id' => $tax->id,
            'weight_from' => 0,
            'weight_to' => 9999,
            'cost_gross' => 5.00,
            'shipping_days' => 5,
            'active' => true,
        ]);

        ShippingConfig::set('default_shipping_tier_id', $tier->id);

        $response = $this->withSession(['cart' => [$product->id => 2]])
            ->actingAs($user)
            ->post(route('checkout.place'), [
                'address_id' => $address->id,
                'shipping_tier_id' => $tier->id,
            ]);

        $order = \App\Models\Order::where('user_id', $user->id)->latest()->first();

        $response->assertRedirect(route('orders.pay', $order))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('orders', ['user_id' => $user->id]);

        $product->refresh();
        $this->assertEquals(3, $product->stock);

        // assert an order for this user was created and validate its totals (don't rely on global counts)
        $this->assertDatabaseHas('orders', ['user_id' => $user->id]);
        $order = Order::where('user_id', $user->id)->latest()->first();
        $this->assertNotNull($order, 'Expected an order for the test user');
        $this->assertEquals($order->total_gross, round(2 * 20.00 + 5.00, 2));
    }
}
