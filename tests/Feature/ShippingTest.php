<?php

namespace Tests\Feature;

use App\Models\Country;
use App\Models\Order;
use App\Models\Product;
use App\Models\ShippingConfig;
use App\Models\ShippingTier;
use App\Models\Tax;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShippingTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_shipping_tiers_falls_back_to_weight_when_no_region_match()
    {
        $user = User::factory()->create();

        $tax = Tax::create(['name' => 'VAT', 'percentage' => 23, 'is_active' => true]);

        $product = Product::create([
            'tax_id' => $tax->id,
            'price' => 30.00,
            'stock' => 5,
            'weight' => 2.0,
            'active' => true,
        ]);

        // Create a paid tier that matches by weight but has no region defined
        $paidTier = ShippingTier::create([
            'name_en' => 'Weight Only',
            'name_pt' => 'Weight Only',
            'tax_id' => $tax->id,
            'weight_from' => 0,
            'weight_to' => 9999,
            'cost_gross' => 8.00,
            'shipping_days' => 4,
            'active' => true,
        ]);

        $response = $this->withSession(['cart' => [$product->id => 1]])
            ->actingAs($user)
            ->postJson(route('checkout.shipping-tiers'), ['postal_code' => '9999']);

        $response->assertStatus(200);
        $json = $response->json();
        $this->assertFalse(empty($json['tiers']));
        $tiers = collect($json['tiers']);
        $this->assertTrue($tiers->contains(fn ($t) => ($t['id'] ?? null) === $paidTier->id));
    }

    public function test_expected_delivery_date_includes_production_time_for_backorders()
    {
        $user = User::factory()->create();

        $tax = Tax::create(['name' => 'VAT', 'percentage' => 23, 'is_active' => true]);

        // Product is backorder and has production_time
        $product = Product::create([
            'tax_id' => $tax->id,
            'price' => 50.00,
            'stock' => 0,
            'weight' => 1.0,
            'active' => true,
            'is_backorder' => true,
            'production_time' => 3, // working days
        ]);

        $country = Country::firstOrCreate(['iso_alpha2' => 'PT'], ['name_pt' => 'Portugal', 'name_en' => 'Portugal', 'country_code' => '351', 'is_active' => true]);

        $address = $user->addresses()->create([
            'title' => 'Home',
            'address_line_1' => 'Rua Teste 2',
            'postal_code' => '2000-000',
            'city' => 'Lisbon',
            'country_id' => $country->id,
            'is_default' => true,
        ]);

        $tier = ShippingTier::create([
            'name_en' => 'Slow',
            'name_pt' => 'Slow',
            'tax_id' => $tax->id,
            'weight_from' => 0,
            'weight_to' => 9999,
            'cost_gross' => 6.00,
            'shipping_days' => 2,
            'active' => true,
        ]);

        ShippingConfig::set('default_shipping_tier_id', $tier->id);

        // Freeze time to compute expected date deterministically
        Carbon::setTestNow(Carbon::create(2026, 1, 15, 9, 0, 0));

        $response = $this->withSession(['cart' => [$product->id => 1]])
            ->actingAs($user)
            ->post(route('checkout.place'), [
                'address_id' => $address->id,
                'shipping_tier_id' => $tier->id,
            ]);

        $order = \App\Models\Order::where('user_id', $user->id)->latest()->first();

        $response->assertRedirect(route('orders.pay', $order));

        // ensure an order was created for this user and compute expected delivery on that order
        $this->assertDatabaseHas('orders', ['user_id' => $user->id]);
        $order = Order::where('user_id', $user->id)->latest()->first();

        // expected working days = production_time (3) + shipping_days (2) = 5 working days
        // Compute expected date by adding working days (skip weekends) to the start date
        $expected = Carbon::create(2026, 1, 15, 9, 0, 0);
        $daysAdded = 0;
        while ($daysAdded < 5) {
            $expected->addDay();
            if (! $expected->isWeekend()) {
                $daysAdded++;
            }
        }

        $this->assertEquals($expected->toDateString(), $order->expected_delivery_date->toDateString());

        // Clear frozen time
        Carbon::setTestNow();
    }
}
