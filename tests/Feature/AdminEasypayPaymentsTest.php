<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Order;
use App\Models\User;
use App\Models\Role;
use App\Models\EasypayPayload;
use App\Models\EasypayCheckoutSession;
use App\Models\EasypayPayment;

class AdminEasypayPaymentsTest extends TestCase
{
    public function test_admin_can_view_payments_index()
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
        $order->update(['order_number' => 'ORD-PAY-1']);

        $payload = EasypayPayload::create(['order_id' => $order->id, 'payload' => ['x' => 1]]);
        $session = EasypayCheckoutSession::create(['order_id' => $order->id, 'payload_id' => $payload->id, 'status' => 'created']);

        $payment = EasypayPayment::create([
            'payment_id' => 'pay_1',
            'checkout_id' => $session->checkout_id ?? null,
            'order_id' => $order->id,
            'payment_status' => 'paid',
            'payment_method' => 'card',
            'card_last_digits' => '4242',
            'paid_at' => now(),
            'raw_response' => ['ok' => true],
        ]);

        $this->actingAs($admin)
            ->get(route('admin.orders.payments.index'))
            ->assertStatus(200)
            ->assertSee('Payments')
            ->assertSee($order->order_number)
            ->assertSee('View');
    }

    public function test_filters_on_payments_index_work()
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
        $orderA->update(['order_number' => 'ORD-PAY-A']);
        $orderB->update(['order_number' => 'ORD-PAY-B']);

        $pA = EasypayPayment::create(['payment_id' => 'pA', 'order_id' => $orderA->id, 'payment_status' => 'paid']);
        $pB = EasypayPayment::create(['payment_id' => 'pB', 'order_id' => $orderB->id, 'payment_status' => 'paid']);

        \Illuminate\Support\Facades\DB::table('easypay_payments')->where('id', $pA->id)->update(['created_at' => now()->subDays(4)]);
        \Illuminate\Support\Facades\DB::table('easypay_payments')->where('id', $pB->id)->update(['created_at' => now()->subDay()]);

        $this->actingAs($admin)
            ->get(route('admin.orders.payments.index', ['from_paid_date' => now()->subDays(2)->format('Y-m-d')]))
            ->assertStatus(200)
            ->assertSee('ORD-PAY-B')
            ->assertDontSee('ORD-PAY-A');
    }

    public function test_admin_can_view_payment_show()
    {
        $role = Role::firstOrCreate(['name' => 'admin']);
        $admin = User::factory()->create();
        $admin->roles()->attach($role->id);

        $country = \App\Models\Country::firstOrCreate(['iso_alpha2' => 'PT'], ['name_pt' => 'Portugal', 'name_en' => 'Portugal', 'country_code' => '351', 'is_active' => true]);
        $user = User::factory()->create();
        $addr = \App\Models\Address::factory()->create(['country_id' => $country->id, 'user_id' => $user->id]);
        $order = Order::factory()->create(['user_id' => $user->id, 'address_id' => $addr->id]);
        $order->update(['order_number' => 'ORD-PAY-SHOW']);

        $payload = EasypayPayload::create(['order_id' => $order->id, 'payload' => ['customer' => ['name' => 'ACME']]]);
        $session = EasypayCheckoutSession::create(['order_id' => $order->id, 'payload_id' => $payload->id, 'status' => 'created', 'checkout_id' => 'chk_1']);
        $payment = EasypayPayment::create(['payment_id' => 'pay_1', 'checkout_id' => $session->checkout_id, 'order_id' => $order->id, 'payment_status' => 'paid', 'payment_method' => 'mb', 'mb_entity' => '123', 'mb_reference' => '456', 'raw_response' => ['x' => 1]]);

        $this->actingAs($admin)
            ->get(route('admin.orders.payments.show', $payment))
            ->assertStatus(200)
            ->assertSee('Easypay payment')
            ->assertSee('View checkout')
            ->assertSee('MB entity')
            ->assertSee('123')
            ->assertSee('"x": 1');
    }

    public function test_orders_index_includes_payments_link()
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
        $order->update(['order_number' => 'ORD-PAY-IDX']);

        $this->actingAs($admin)
            ->get(route('admin.orders.index'))
            ->assertStatus(200)
            ->assertSee(route('admin.orders.payments.index', ['order_number' => $order->order_number]))
            ->assertSee('Payments')
            ->assertSee('View payments');
    }

    public function test_order_show_includes_payments_link()
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
        $order->update(['order_number' => 'ORD-PAY-BTN']);

        $this->actingAs($admin)
            ->get(route('admin.orders.show', $order))
            ->assertStatus(200)
            ->assertSee(route('admin.orders.payments.index', ['order_number' => $order->order_number]))
            ->assertSee('View payments');
    }
}
