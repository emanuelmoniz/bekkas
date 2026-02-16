<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Country;
use App\Models\Order;
use App\Models\Product;
use App\Models\Tax;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreSwitchTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_store_pages_are_unavailable_when_store_disabled()
    {
        config(['app.store_enabled' => false]);

        $tax = Tax::create(['name' => 'VAT', 'percentage' => 23, 'is_active' => true]);

        $product = Product::create([
            'tax_id' => $tax->id,
            'price' => 10.00,
            'stock' => 10,
            'weight' => 1.0,
            'active' => true,
        ]);

        // products index/show should be 404
        $this->get(route('products.index'))->assertStatus(404);
        $this->get(route('products.show', $product))->assertStatus(404);

        // cart page should be disabled
        $this->get(route('cart.index'))->assertStatus(404);

        // checkout (authenticated) should be disabled
        $user = User::factory()->create();
        $country = Country::firstOrCreate(['iso_alpha2' => 'PT'], ['name_pt' => 'Portugal', 'name_en' => 'Portugal', 'country_code' => '351', 'is_active' => true]);
        $address = $user->addresses()->create([
            'title' => 'Home',
            'address_line_1' => 'Rua X',
            'postal_code' => '1000-001',
            'city' => 'Lisbon',
            'country_id' => $country->id,
            'is_default' => true,
        ]);

        $this->withSession(['cart' => [$product->id => 1]])
            ->actingAs($user)
            ->get(route('checkout.index'))
            ->assertStatus(404);
    }

    public function test_place_order_is_blocked_when_store_disabled()
    {
        config(['app.store_enabled' => false]);

        $user = User::factory()->create();
        $tax = Tax::create(['name' => 'VAT', 'percentage' => 23, 'is_active' => true]);

        $product = Product::create([
            'tax_id' => $tax->id,
            'price' => 15.00,
            'stock' => 5,
            'weight' => 1.0,
            'active' => true,
        ]);

        $country = Country::firstOrCreate(['iso_alpha2' => 'PT'], ['name_pt' => 'Portugal', 'name_en' => 'Portugal', 'country_code' => '351', 'is_active' => true]);

        $address = $user->addresses()->create([
            'title' => 'Home',
            'address_line_1' => 'Rua B',
            'postal_code' => '1000-001',
            'city' => 'Lisbon',
            'country_id' => $country->id,
            'is_default' => true,
        ]);

        $this->withSession(['cart' => [$product->id => 1]])
            ->actingAs($user)
            ->post(route('checkout.place'), ['address_id' => $address->id])
            ->assertStatus(404);

        $this->assertDatabaseMissing('orders', ['user_id' => $user->id]);
    }

    public function test_existing_orders_and_payment_page_still_accessible_when_store_disabled()
    {
        config(['app.store_enabled' => false]);

        $user = User::factory()->create();
        $country = Country::firstOrCreate(['iso_alpha2' => 'PT'], ['name_pt' => 'Portugal', 'name_en' => 'Portugal', 'country_code' => '351', 'is_active' => true]);

        $address = $user->addresses()->create([
            'title' => 'Home',
            'address_line_1' => 'Rua B',
            'postal_code' => '1000-001',
            'city' => 'Lisbon',
            'country_id' => $country->id,
            'is_default' => true,
        ]);

        // Create an order for the user (factory)
        $order = \Database\Factories\OrderFactory::new()->for($user)->for($address)->create(['status' => 'WAITING_PAYMENT']);

        // Orders index & show must still be available
        $this->actingAs($user)
            ->get(route('orders.index'))
            ->assertOk()
            ->assertSee((string) $order->id);

        $this->actingAs($user)
            ->get(route('orders.show', $order))
            ->assertOk()
            ->assertSee(number_format($order->total_gross, 2));

        // Payment page (Easypay SDK orchestration) should still be reachable for existing WAITING_PAYMENT orders
        $this->actingAs($user)
            ->get(route('orders.pay', $order))
            ->assertOk();
    }
}
