<?php

namespace Tests\Feature;

use App\Models\EasypayPayload;
use App\Models\Order;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminEasypayPayloadsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_payloads_index()
    {
        $role = Role::firstOrCreate(['name' => 'admin']);
        $admin = User::factory()->create();
        $admin->roles()->attach($role->id);

        // create a stable country + address so factories don't attempt the same country insert twice
        $country = \App\Models\Country::firstOrCreate(['iso_alpha2' => 'PT'], ['name_pt' => 'Portugal', 'name_en' => 'Portugal', 'country_code' => '351', 'is_active' => true]);
        $user = User::factory()->create();
        $address = \App\Models\Address::factory()->create(['country_id' => $country->id, 'user_id' => $user->id]);

        $order = Order::factory()->create(['user_id' => $user->id, 'address_id' => $address->id]);
        // set a predictable order_number (creating hook will otherwise overwrite any provided value)
        $order->update(['order_number' => 'ORD-100']);

        $payload = EasypayPayload::create([
            'order_id' => $order->id,
            'payload' => ['customer' => ['name' => 'Acme Corp']],
        ]);

        $this->assertDatabaseHas('easypay_payloads', ['order_id' => $order->id]);

        // ensure at least one payload exists for this order (avoid asserting global counts which are brittle)
        $this->assertNotNull(\App\Models\EasypayPayload::where('order_id', $order->id)->first());
        $this->assertNotNull(\App\Models\EasypayPayload::with('order')->where('order_id', $order->id)->first()->order);

        $resp = $this->actingAs($admin)->get(route('admin.orders.payloads.index'));

        $resp->assertStatus(200)
            ->assertSee('Payloads')
            ->assertSee($order->order_number)
            ->assertSee($payload->created_at->format('d/m/Y'))
            ->assertSee('View');
    }

    public function test_filters_on_payloads_index_work()
    {
        $role = Role::firstOrCreate(['name' => 'admin']);
        $admin = User::factory()->create();
        $admin->roles()->attach($role->id);

        // avoid duplicate country creation by creating explicit country + addresses
        $country = \App\Models\Country::firstOrCreate(['iso_alpha2' => 'PT'], ['name_pt' => 'Portugal', 'name_en' => 'Portugal', 'country_code' => '351', 'is_active' => true]);
        $userA = User::factory()->create();
        $addrA = \App\Models\Address::factory()->create(['country_id' => $country->id, 'user_id' => $userA->id]);
        $orderA = Order::factory()->create(['created_at' => now()->subDays(5), 'user_id' => $userA->id, 'address_id' => $addrA->id]);
        $orderA->update(['order_number' => 'AAA-1']);

        $userB = User::factory()->create();
        $addrB = \App\Models\Address::factory()->create(['country_id' => $country->id, 'user_id' => $userB->id]);
        $orderB = Order::factory()->create(['created_at' => now()->subDays(1), 'user_id' => $userB->id, 'address_id' => $addrB->id]);
        $orderB->update(['order_number' => 'BBB-2']);

        $pA = EasypayPayload::create(['order_id' => $orderA->id, 'payload' => ['x' => 1]]);
        $pB = EasypayPayload::create(['order_id' => $orderB->id, 'payload' => ['x' => 2]]);

        // created_at is not mass-assignable on EasypayPayload; set explicitly via query for deterministic dates
        \Illuminate\Support\Facades\DB::table('easypay_payloads')->where('id', $pA->id)->update(['created_at' => now()->subDays(4)]);
        \Illuminate\Support\Facades\DB::table('easypay_payloads')->where('id', $pB->id)->update(['created_at' => now()->subDay()]);

        $this->assertDatabaseHas('easypay_payloads', ['order_id' => $orderA->id]);
        $this->assertDatabaseHas('easypay_payloads', ['order_id' => $orderB->id]);

        // filter by order_number
        $this->actingAs($admin)
            ->get(route('admin.orders.payloads.index', ['order_number' => 'AAA']))
            ->assertStatus(200)
            ->assertSee('AAA-1')
            ->assertDontSee('BBB-2');

        // filter by payload date
        $resp = $this->actingAs($admin)->get(route('admin.orders.payloads.index', ['from_payload_date' => now()->subDays(2)->toDateString()]));

        $resp->assertStatus(200)
            ->assertSee('BBB-2')
            ->assertDontSee('AAA-1');
    }

    public function test_admin_can_view_payload_show()
    {
        $role = Role::firstOrCreate(['name' => 'admin']);
        $admin = User::factory()->create();
        $admin->roles()->attach($role->id);

        $country = \App\Models\Country::firstOrCreate(['iso_alpha2' => 'PT'], ['name_pt' => 'Portugal', 'name_en' => 'Portugal', 'country_code' => '351', 'is_active' => true]);
        $user = User::factory()->create();
        $addr = \App\Models\Address::factory()->create(['country_id' => $country->id, 'user_id' => $user->id]);

        $order = Order::factory()->create(['user_id' => $user->id, 'address_id' => $addr->id]);
        $order->update(['order_number' => 'ORD-SSH-1']);

        $payload = EasypayPayload::create(['order_id' => $order->id, 'payload' => ['customer' => ['name' => 'ACME']]]);

        $this->actingAs($admin)
            ->get(route('admin.orders.payloads.show', $payload))
            ->assertStatus(200)
            ->assertSee('Easypay payload')
            ->assertSee('ORD-SSH-1')
            ->assertSee('"customer"')
            ->assertSee('ACME')
            ->assertSee('Delete');
    }

    public function test_admin_can_delete_payload_from_show()
    {
        $role = Role::firstOrCreate(['name' => 'admin']);
        $admin = User::factory()->create();
        $admin->roles()->attach($role->id);

        $country = \App\Models\Country::firstOrCreate(['iso_alpha2' => 'PT'], ['name_pt' => 'Portugal', 'name_en' => 'Portugal', 'country_code' => '351', 'is_active' => true]);
        $user = User::factory()->create();
        $addr = \App\Models\Address::factory()->create(['country_id' => $country->id, 'user_id' => $user->id]);

        $order = Order::factory()->create(['user_id' => $user->id, 'address_id' => $addr->id]);
        $payload = EasypayPayload::create(['order_id' => $order->id, 'payload' => ['x' => 1]]);

        $this->actingAs($admin)
            ->get(route('admin.orders.payloads.show', $payload))
            ->assertStatus(200)
            ->assertSee('Delete');

        $this->actingAs($admin)
            ->delete(route('admin.orders.payloads.destroy', $payload))
            ->assertRedirect();

        $this->assertDatabaseMissing('easypay_payloads', ['id' => $payload->id]);
    }

    public function test_admin_can_delete_payload_from_index()
    {
        $role = Role::firstOrCreate(['name' => 'admin']);
        $admin = User::factory()->create();
        $admin->roles()->attach($role->id);

        $country = \App\Models\Country::firstOrCreate(['iso_alpha2' => 'PT'], ['name_pt' => 'Portugal', 'name_en' => 'Portugal', 'country_code' => '351', 'is_active' => true]);
        $user = User::factory()->create();
        $addr = \App\Models\Address::factory()->create(['country_id' => $country->id, 'user_id' => $user->id]);

        $order = Order::factory()->create(['user_id' => $user->id, 'address_id' => $addr->id]);
        $order->update(['order_number' => 'IDX-DEL-1']);
        $p = EasypayPayload::create(['order_id' => $order->id, 'payload' => ['x' => 1]]);

        $this->actingAs($admin)
            ->get(route('admin.orders.payloads.index'))
            ->assertStatus(200)

            ->assertSee('IDX-DEL-1');

        $this->actingAs($admin)
            ->delete(route('admin.orders.payloads.destroy', $p))
            ->assertRedirect();

        $this->assertDatabaseMissing('easypay_payloads', ['id' => $p->id]);
    }

    public function test_order_show_includes_payload_button()
    {
        $role = Role::firstOrCreate(['name' => 'admin']);
        $admin = User::factory()->create();
        $admin->roles()->attach($role->id);

        $country = \App\Models\Country::firstOrCreate(['iso_alpha2' => 'PT'], ['name_pt' => 'Portugal', 'name_en' => 'Portugal', 'country_code' => '351', 'is_active' => true]);
        $user = User::factory()->create();
        $addr = \App\Models\Address::factory()->create(['country_id' => $country->id, 'user_id' => $user->id]);

        $order = Order::factory()->create(['user_id' => $user->id, 'address_id' => $addr->id]);
        $order->update(['order_number' => 'ORD-BTN-1']);

        $payload = EasypayPayload::create(['order_id' => $order->id, 'payload' => ['x' => 1]]);

        $this->actingAs($admin)
            ->get(route('admin.orders.show', $order))
            ->assertStatus(200)
            ->assertSee(route('admin.orders.payloads.show', $payload))
            ->assertSee('View payload')
            ->assertSee(route('admin.orders.checkouts.index', ['order_number' => $order->order_number]))
            ->assertSee('View checkout sessions');
    }

    public function test_admin_can_create_payload_from_order_show()
    {
        $role = Role::firstOrCreate(['name' => 'admin']);
        $admin = User::factory()->create();
        $admin->roles()->attach($role->id);

        $country = \App\Models\Country::firstOrCreate(['iso_alpha2' => 'PT'], ['name_pt' => 'Portugal', 'name_en' => 'Portugal', 'country_code' => '351', 'is_active' => true]);
        $user = User::factory()->create();
        $addr = \App\Models\Address::factory()->create(['country_id' => $country->id, 'user_id' => $user->id]);

        $order = Order::factory()->create(['user_id' => $user->id, 'address_id' => $addr->id]);
        $order->update(['order_number' => 'ORD-CREATE-1']);

        $this->assertDatabaseMissing('easypay_payloads', ['order_id' => $order->id]);

        $this->actingAs($admin)
            ->get(route('admin.orders.show', $order))
            ->assertStatus(200)
            ->assertSee('Create payload');

        $this->actingAs($admin)
            ->post(route('admin.orders.payloads.store', $order))
            ->assertRedirect();

        $this->assertDatabaseHas('easypay_payloads', ['order_id' => $order->id]);
    }
}
