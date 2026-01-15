<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Role;
use App\Models\Product;
use App\Models\Tax;
use App\Models\Country;
use App\Models\Address;
use App\Models\ShippingTier;
use App\Models\ShippingConfig;
use App\Models\Order;

class OrdersTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_cannot_view_another_users_order()
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $tax = Tax::create(['name' => 'VAT', 'percentage' => 23, 'is_active' => true]);

        $product = Product::create([
            'tax_id' => $tax->id,
            'price' => 15.00,
            'stock' => 5,
            'weight' => 1.0,
            'active' => true,
        ]);

        $country = Country::create(['name_pt' => 'Portugal', 'name_en' => 'Portugal', 'iso_alpha2' => 'PT', 'country_code' => '351', 'is_active' => true]);

        $address = $userB->addresses()->create([
            'title' => 'Home',
            'address_line_1' => 'Rua B',
            'postal_code' => '1000-001',
            'city' => 'Lisbon',
            'country_id' => $country->id,
            'is_default' => true,
        ]);

        // Place an order for userB
        $this->withSession(['cart' => [$product->id => 1]])
            ->actingAs($userB)
            ->post(route('checkout.place'), [
                'address_id' => $address->id,
            ]);

        $this->assertDatabaseCount('orders', 1);
        $order = Order::first();

        // userA should be forbidden to view userB's order
        $this->actingAs($userA)
            ->get(route('orders.show', $order))
            ->assertForbidden();
    }

    public function test_admin_cancel_and_reinstate_updates_stock()
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

        $country = Country::create(['name_pt' => 'Portugal', 'name_en' => 'Portugal', 'iso_alpha2' => 'PT', 'country_code' => '351', 'is_active' => true]);

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

        // Create order and add an item referencing the test product (deterministic)
        $order = \Database\Factories\OrderFactory::new()->for($user)->for($address)->create();

        $order->items()->create([
            'product_id' => $product->id,
            'quantity' => 2,
            'was_backordered' => false,
            'unit_price_gross' => 20.00,
            'tax_percentage' => 23,
            'unit_weight' => 1.0,
            'total_net' => round((20.00 / 1.23) * 2, 2),
            'total_tax' => round((20.00 * 2) - ((20.00 / 1.23) * 2), 2),
            'total_gross' => round(20.00 * 2, 2),
        ]);

        // Simulate stock decrement that would have happened on place
        $product->decrement('stock', 2);
        $product->refresh();
        $this->assertEquals(3, $product->stock);

        // Sanity checks: order item exists and has expected quantity
        $this->assertDatabaseHas('order_items', ['order_id' => $order->id, 'product_id' => $product->id, 'quantity' => 2]);
        $item = $order->items()->first();
        $this->assertNotNull($item, 'Order item missing from database');
        $this->assertEquals(2, $item->quantity, 'Order item quantity mismatch');
        $this->assertFalse((bool) $item->was_backordered, 'Order item unexpectedly marked as backordered');

        // Create admin
        $admin = User::factory()->create();
        $role = Role::create(['name' => 'admin']);
        $admin->roles()->attach($role->id);

        // Cancel the order as admin
        $this->actingAs($admin)
            ->patch(route('admin.orders.update', $order), ['status' => 'CANCELED'])
            ->assertRedirect(route('admin.orders.show', $order))
            ->assertSessionHas('success');

        $product->refresh();
        // stock should be restored to original 5
        $this->assertEquals(5, $product->stock, 'Stock did not restore after cancel; check OrderController increment logic');

        // Reinstate (change status from CANCELED to PROCESSING) - stock should be decremented
        $this->actingAs($admin)
            ->patch(route('admin.orders.update', $order), ['status' => 'PROCESSING'])
            ->assertRedirect(route('admin.orders.show', $order))
            ->assertSessionHas('success');

        $product->refresh();
        $this->assertEquals(3, $product->stock);
    }
}
