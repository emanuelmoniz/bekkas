<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Order;
use App\Models\User;
use App\Models\Role;
use App\Models\EasypayPayload;
use App\Models\EasypayCheckoutSession;

class AdminEasypayCheckoutsTest extends TestCase
{
    public function test_admin_can_view_checkouts_index()
    {
        $role = Role::firstOrCreate(['name' => 'admin']);
        $admin = User::factory()->create();
        $admin->roles()->attach($role->id);

        \Illuminate\Support\Facades\DB::table('countries')->updateOrInsert([
            'iso_alpha2' => 'PT'
        ], [
            'name_pt' => 'Portugal', 'name_en' => 'Portugal', 'country_code' => '351', 'is_active' => true
        ]);

        $countryId = \Illuminate\Support\Facades\DB::table('countries')->where('iso_alpha2', 'PT')->value('id');
        $user = User::factory()->create();
        $addr = \App\Models\Address::factory()->create(['country_id' => $countryId, 'user_id' => $user->id]);
        $order = Order::factory()->create(['user_id' => $user->id, 'address_id' => $addr->id]);
        $order->update(['order_number' => 'ORD-CH-1']);

        $payload = EasypayPayload::create(['order_id' => $order->id, 'payload' => ['x' => 1]]);
        $session = EasypayCheckoutSession::create(['order_id' => $order->id, 'payload_id' => $payload->id, 'status' => 'created', 'message' => json_encode(['ok' => true])]);

        $this->actingAs($admin)
            ->get(route('admin.orders.checkouts.index'))
            ->assertStatus(200)
            ->assertSee('Checkout sessions')
            ->assertSee($order->order_number)
            ->assertSee($session->created_at->format('d/m/Y'))
            ->assertSee('View');
    }

    public function test_filters_on_checkouts_index_work()
    {
        $role = Role::firstOrCreate(['name' => 'admin']);
        $admin = User::factory()->create();
        $admin->roles()->attach($role->id);

        \Illuminate\Support\Facades\DB::table('countries')->updateOrInsert([
            'iso_alpha2' => 'PT'
        ], [
            'name_pt' => 'Portugal', 'name_en' => 'Portugal', 'country_code' => '351', 'is_active' => true
        ]);
        $countryId = \Illuminate\Support\Facades\DB::table('countries')->where('iso_alpha2', 'PT')->value('id');
        $user = User::factory()->create();
        $addrA = \App\Models\Address::factory()->create(['country_id' => $countryId, 'user_id' => $user->id]);
        $addrB = \App\Models\Address::factory()->create(['country_id' => $countryId, 'user_id' => $user->id]);
        $orderA = Order::factory()->create(['user_id' => $user->id, 'address_id' => $addrA->id]);
        $orderB = Order::factory()->create(['user_id' => $user->id, 'address_id' => $addrB->id]);
        $orderA->update(['order_number' => 'ORD-CH-A']);
        $orderB->update(['order_number' => 'ORD-CH-B']);

        $sA = EasypayCheckoutSession::create(['order_id' => $orderA->id, 'message' => json_encode(['a' => 1])]);
        $sB = EasypayCheckoutSession::create(['order_id' => $orderB->id, 'message' => json_encode(['b' => 2])]);

        \Illuminate\Support\Facades\DB::table('easypay_checkout_sessions')->where('id', $sA->id)->update(['created_at' => now()->subDays(4)]);
        \Illuminate\Support\Facades\DB::table('easypay_checkout_sessions')->where('id', $sB->id)->update(['created_at' => now()->subDay()]);

        $this->actingAs($admin)
            ->get(route('admin.orders.checkouts.index', ['from_session_date' => now()->subDays(2)->format('Y-m-d')]))
            ->assertStatus(200)
            ->assertSee('ORD-CH-B')
            ->assertDontSee('ORD-CH-A');
    }

    public function test_admin_can_view_checkout_show()
    {
        $role = Role::firstOrCreate(['name' => 'admin']);
        $admin = User::factory()->create();
        $admin->roles()->attach($role->id);

        $country = \App\Models\Country::firstOrCreate(['iso_alpha2' => 'PT'], ['name_pt' => 'Portugal', 'name_en' => 'Portugal', 'country_code' => '351', 'is_active' => true]);
        $user = User::factory()->create();
        $addr = \App\Models\Address::factory()->create(['country_id' => $country->id, 'user_id' => $user->id]);
        $order = Order::factory()->create(['user_id' => $user->id, 'address_id' => $addr->id]);
        $order->update(['order_number' => 'ORD-CH-SHOW']);

        $payload = EasypayPayload::create(['order_id' => $order->id, 'payload' => ['customer' => ['name' => 'ACME']]]);
        $session = EasypayCheckoutSession::create(['order_id' => $order->id, 'payload_id' => $payload->id, 'status' => 'created', 'message' => json_encode(['x' => 1])]);

        $this->actingAs($admin)
            ->get(route('admin.orders.checkouts.show', $session))
            ->assertStatus(200)
            ->assertSee('Easypay checkout session')
            ->assertSee('Payload')
            ->assertSee('View payload')
            ->assertSee('"x": 1');
    }

    public function test_order_show_includes_checkouts_link()
    {
        $role = Role::firstOrCreate(['name' => 'admin']);
        $admin = User::factory()->create();
        $admin->roles()->attach($role->id);

        \Illuminate\Support\Facades\DB::table('countries')->updateOrInsert([
            'iso_alpha2' => 'PT'
        ], [
            'name_pt' => 'Portugal', 'name_en' => 'Portugal', 'country_code' => '351', 'is_active' => true
        ]);
        $countryId = \Illuminate\Support\Facades\DB::table('countries')->where('iso_alpha2', 'PT')->value('id');
        $user = User::factory()->create();
        $addr = \App\Models\Address::factory()->create(['country_id' => $countryId, 'user_id' => $user->id]);
        $order = Order::factory()->create(['user_id' => $user->id, 'address_id' => $addr->id]);
        $order->update(['order_number' => 'ORD-CH-BTN']);

        $this->actingAs($admin)
            ->get(route('admin.orders.show', $order))
            ->assertStatus(200)
            ->assertSee(route('admin.orders.checkouts.index', ['order_number' => $order->order_number]))
            ->assertSee('View checkout sessions');
    }
}
