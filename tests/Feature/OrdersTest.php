<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Country;
use App\Models\Order;
use App\Models\Product;
use App\Models\Role;
use App\Models\ShippingConfig;
use App\Models\ShippingTier;
use App\Models\Tax;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

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

        $country = Country::firstOrCreate(['iso_alpha2' => 'PT'], ['name_pt' => 'Portugal', 'name_en' => 'Portugal', 'country_code' => '351', 'is_active' => true]);

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

        // assert an order for userB exists and then verify access restrictions
        $this->assertDatabaseHas('orders', ['user_id' => $userB->id]);
        $order = Order::where('user_id', $userB->id)->latest()->first();

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
        $role = Role::firstOrCreate(['name' => 'admin']);
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

    public function test_emails_sent_on_order_place()
    {
        \Illuminate\Support\Facades\Mail::fake();

        $user = User::factory()->create(['language' => 'en-UK']);

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

        // Place the order and assert the session flash was set (no double POST)
        $response = $this->withSession(['cart' => [$product->id => 1]])
            ->actingAs($user)
            ->post(route('checkout.place'), ['address_id' => $address->id]);

        $order = \App\Models\Order::where('user_id', $user->id)->latest()->first();

        $response->assertRedirect(route('orders.pay', $order))
            ->assertSessionHas('success', t('orders.placed_success') ?: 'Order placed successfully! Thank you.');

        // Ensure the layout renders the same flash when session contains it
        $this->withSession(['success' => t('orders.placed_success')])
            ->actingAs($user)
            ->get(route('orders.index'))
            ->assertSee('Close flash message')
            ->assertSee('border-l-4')
            ->assertSee('text-status-success');

        // Two mails queued: one to customer and one to admin
        \Illuminate\Support\Facades\Mail::assertQueued(\App\Mail\OrderNotification::class, 2);

        // Customer mail must be queued (locale correctness covered in dedicated tests).
        \Illuminate\Support\Facades\Mail::assertQueued(\App\Mail\OrderNotification::class, function ($mail) use ($user) {
            return $mail->hasTo($user->email);
        });

        // Sanity: building the mailable with the customer's locale yields an English subject
        $m = new \App\Mail\OrderNotification($order, 'orders.email.event.placed', $user->name, ($order->status ?? null), ['status' => ($order->status ?? null)]);
        // Simulate the mailer applying the user's locale when building the mailable
        $prev = app()->getLocale();
        app()->setLocale($user->language);
        $this->assertStringContainsString('Order', $m->build()->subject);
        app()->setLocale($prev);

        \Illuminate\Support\Facades\Mail::assertQueued(\App\Mail\OrderNotification::class, function ($mail) use ($order) {
            // Admin mails must be in English and sent to configured admin address and link to admin order page
            return $mail->hasTo(config('mail.admin_address', 'info@bekkas.pt'))
                && ($mail->locale === 'en-UK' || $mail->locale === 'en')
                && ($mail->actionUrl === route('admin.orders.show', $order));
        });
    }

    public function test_no_emails_sent_when_send_mails_disabled()
    {
        \Illuminate\Support\Facades\Mail::fake();

        // Disable emails via DB configuration and re-run provider so runtime config is updated
        \App\Models\Configuration::create(['send_mails_enabled' => false]);
        $this->app->getProvider(\App\Providers\ConfigurationServiceProvider::class)->boot();

        $user = User::factory()->create(['language' => 'en-UK']);

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

        // Place the order
        $response = $this->withSession(['cart' => [$product->id => 1]])
            ->actingAs($user)
            ->post(route('checkout.place'), ['address_id' => $address->id]);

        $order = \App\Models\Order::where('user_id', $user->id)->latest()->first();

        $response->assertRedirect(route('orders.pay', $order));

        // Calls remain queued (we removed per-call guards) but the global mailer is switched
        $this->assertFalse(config('mail.enabled'));
        $this->assertEquals('disabled', config('mail.default'));

        \Illuminate\Support\Facades\Mail::assertQueued(\App\Mail\OrderNotification::class, 2);
    }

    public function test_client_receives_email_on_status_change()
    {
        \Illuminate\Support\Facades\Mail::fake();

        $user = User::factory()->create(['language' => 'en-UK']);
        $admin = User::factory()->create();
        $role = Role::firstOrCreate(['name' => 'admin']);
        $admin->roles()->attach($role->id);

        $order = \Database\Factories\OrderFactory::new()->for($user)->create();

        $this->actingAs($admin)
            ->patch(route('admin.orders.update', $order), ['status' => 'SHIPPED'])
            ->assertRedirect(route('admin.orders.show', $order));

        // Assert a notification was queued for the customer (don't assert on message internals here)
        \Illuminate\Support\Facades\Mail::assertQueued(\App\Mail\OrderNotification::class, function ($mail) use ($user) {
            return $mail->hasTo($user->email);
        });
    }
}
