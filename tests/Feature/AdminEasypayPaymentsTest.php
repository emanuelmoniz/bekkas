<?php

namespace Tests\Feature;

use App\Models\EasypayCheckoutSession;
use App\Models\EasypayPayload;
use App\Models\EasypayPayment;
use App\Models\Order;
use App\Models\Role;
use App\Models\User;
use Tests\TestCase;

class AdminEasypayPaymentsTest extends TestCase
{
    public function test_admin_can_view_payments_index()
    {
        $role = Role::firstOrCreate(['name' => 'admin']);
        $admin = User::factory()->create();
        $admin->roles()->attach($role->id);

        \Illuminate\Support\Facades\DB::table('countries')->updateOrInsert([
            'iso_alpha2' => 'PT',
        ], [
            'name_pt' => 'Portugal', 'name_en' => 'Portugal', 'country_code' => '351', 'is_active' => true,
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
            'iso_alpha2' => 'PT',
        ], [
            'name_pt' => 'Portugal', 'name_en' => 'Portugal', 'country_code' => '351', 'is_active' => true,
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
        $payment = EasypayPayment::create(['payment_id' => 'pay_1', 'checkout_id' => $session->checkout_id, 'order_id' => $order->id, 'payment_status' => 'paid', 'payment_method' => 'mb', 'mb_entity' => '123', 'mb_reference' => '456', 'capture_id' => 'cap_1', 'raw_response' => ['x' => 1]]);

        $this->actingAs($admin)
            ->get(route('admin.orders.payments.show', $payment))
            ->assertStatus(200)
            ->assertSee('Easypay payment')
            ->assertSee('Checkout')
            ->assertSee('MB entity')
            ->assertSee('123')
            ->assertSee('Capture')
            ->assertSee('cap_1')
            ->assertSee('"x": 1');
    }

    public function test_refund_button_visible_only_for_paid_payment_and_paid_order()
    {
        $role = Role::firstOrCreate(['name' => 'admin']);
        $admin = User::factory()->create();
        $admin->roles()->attach($role->id);

        $country = \App\Models\Country::firstOrCreate(['iso_alpha2' => 'PT'], ['name_pt' => 'Portugal', 'name_en' => 'Portugal', 'country_code' => '351', 'is_active' => true]);
        $user = User::factory()->create();
        $addr = \App\Models\Address::factory()->create(['country_id' => $country->id, 'user_id' => $user->id]);
        $order = Order::factory()->create(['user_id' => $user->id, 'address_id' => $addr->id]);
        $order->markAsPaid('easypay', ['payment_id' => 'pay_1']);

        $payload = EasypayPayload::create(['order_id' => $order->id, 'payload' => ['customer' => ['name' => 'ACME']]]);
        $session = EasypayCheckoutSession::create(['order_id' => $order->id, 'payload_id' => $payload->id, 'status' => 'created', 'checkout_id' => 'chk_1']);
        $payment = EasypayPayment::create(['payment_id' => 'pay_1', 'checkout_id' => $session->checkout_id, 'order_id' => $order->id, 'payment_status' => 'paid', 'payment_method' => 'card', 'paid_at' => now(), 'raw_response' => ['x' => 1]]);

        $this->actingAs($admin)
            ->get(route('admin.orders.payments.show', $payment))
            ->assertStatus(200)
            ->assertSee('Refund')
            ->assertSee('Confirm refund request?');

        // If order isn't marked paid the button must not appear
        $order2 = Order::factory()->create(['user_id' => $user->id, 'address_id' => $addr->id]);
        $p2 = EasypayPayment::create(['payment_id' => 'pay_2', 'order_id' => $order2->id, 'payment_status' => 'paid']);

        $this->actingAs($admin)
            ->get(route('admin.orders.payments.show', $p2))
            ->assertStatus(200)
            ->assertDontSee('Confirm refund request?');
    }

    public function test_admin_can_request_refund_success_response()
    {
        $role = Role::firstOrCreate(['name' => 'admin']);
        $admin = User::factory()->create();
        $admin->roles()->attach($role->id);

        $country = \App\Models\Country::firstOrCreate(['iso_alpha2' => 'PT'], ['name_pt' => 'Portugal', 'name_en' => 'Portugal', 'country_code' => '351', 'is_active' => true]);
        $user = User::factory()->create();
        $addr = \App\Models\Address::factory()->create(['country_id' => $country->id, 'user_id' => $user->id]);
        $order = Order::factory()->create(['user_id' => $user->id, 'address_id' => $addr->id, 'total_gross' => 12.34]);
        $order->markAsPaid('easypay', ['payment_id' => 'pay_1']);

        $payload = EasypayPayload::create(['order_id' => $order->id, 'payload' => ['customer' => ['name' => 'ACME']]]);
        $session = EasypayCheckoutSession::create(['order_id' => $order->id, 'payload_id' => $payload->id, 'status' => 'created', 'checkout_id' => 'chk_1']);
        $payment = EasypayPayment::create(['payment_id' => 'pay_1', 'checkout_id' => $session->checkout_id, 'order_id' => $order->id, 'payment_status' => 'paid', 'payment_method' => 'card', 'paid_at' => now(), 'raw_response' => ['x' => 1]]);

        \Illuminate\Support\Facades\Http::fake([
            'https://api.test.easypay.pt/2.0/refund/pay_1' => \Illuminate\Support\Facades\Http::response(['status' => 'ok', 'message' => 'created', 'id' => 'r_1'], 201),
        ]);

        $this->actingAs($admin)
            ->post(route('admin.orders.payments.refund', $payment))
            ->assertRedirect()
            ->assertSessionHas('success', 'Refund request was submited');

        $order->refresh();
        $this->assertFalse($order->is_refunded, 'Order must not be marked refunded by the admin refund request');

        $payment->refresh();
        $this->assertEquals('r_1', $payment->refund_id);

        // The admin payment show page must display the persisted refund id (right after capture id)
        $this->actingAs($admin)
            ->get(route('admin.orders.payments.show', $payment))
            ->assertStatus(200)
            ->assertSee('Refund request ID')
            ->assertSee('r_1');
    }

    public function test_admin_receives_error_when_refund_fails()
    {
        $role = Role::firstOrCreate(['name' => 'admin']);
        $admin = User::factory()->create();
        $admin->roles()->attach($role->id);

        $country = \App\Models\Country::firstOrCreate(['iso_alpha2' => 'PT'], ['name_pt' => 'Portugal', 'name_en' => 'Portugal', 'country_code' => '351', 'is_active' => true]);
        $user = User::factory()->create();
        $addr = \App\Models\Address::factory()->create(['country_id' => $country->id, 'user_id' => $user->id]);
        $order = Order::factory()->create(['user_id' => $user->id, 'address_id' => $addr->id, 'total_gross' => 12.34]);
        $order->markAsPaid('easypay', ['payment_id' => 'pay_1']);

        $payload = EasypayPayload::create(['order_id' => $order->id, 'payload' => ['customer' => ['name' => 'ACME']]]);
        $session = EasypayCheckoutSession::create(['order_id' => $order->id, 'payload_id' => $payload->id, 'status' => 'created', 'checkout_id' => 'chk_1']);
        $payment = EasypayPayment::create(['payment_id' => 'pay_1', 'checkout_id' => $session->checkout_id, 'order_id' => $order->id, 'payment_status' => 'paid', 'payment_method' => 'card', 'paid_at' => now(), 'raw_response' => ['x' => 1]]);

        \Illuminate\Support\Facades\Http::fake([
            'https://api.test.easypay.pt/2.0/refund/pay_1' => \Illuminate\Support\Facades\Http::response(['status' => 'error', 'message' => ['bad']], 400),
        ]);

        $this->actingAs($admin)
            ->post(route('admin.orders.payments.refund', $payment))
            ->assertRedirect()
            ->assertSessionHas('error', 'Refund could not be processed.');
    }

    public function test_refund_prefers_capture_id_when_present()
    {
        $role = Role::firstOrCreate(['name' => 'admin']);
        $admin = User::factory()->create();
        $admin->roles()->attach($role->id);

        $country = \App\Models\Country::firstOrCreate(['iso_alpha2' => 'PT'], ['name_pt' => 'Portugal', 'name_en' => 'Portugal', 'country_code' => '351', 'is_active' => true]);
        $user = User::factory()->create();
        $addr = \App\Models\Address::factory()->create(['country_id' => $country->id, 'user_id' => $user->id]);
        $order = Order::factory()->create(['user_id' => $user->id, 'address_id' => $addr->id, 'total_gross' => 12.34]);
        $order->markAsPaid('easypay', ['payment_id' => 'pay_1']);

        $payload = EasypayPayload::create(['order_id' => $order->id, 'payload' => ['customer' => ['name' => 'ACME']]]);
        $session = EasypayCheckoutSession::create(['order_id' => $order->id, 'payload_id' => $payload->id, 'status' => 'created', 'checkout_id' => 'chk_1']);
        $payment = EasypayPayment::create(['payment_id' => 'pay_1', 'capture_id' => 'cap_1', 'checkout_id' => $session->checkout_id, 'order_id' => $order->id, 'payment_status' => 'paid', 'payment_method' => 'card', 'paid_at' => now(), 'raw_response' => ['captures' => [['id' => 'cap_1']]]]);

        // Expect refund called against capture id
        \Illuminate\Support\Facades\Http::fake([
            'https://api.test.easypay.pt/2.0/refund/cap_1' => \Illuminate\Support\Facades\Http::response(['status' => 'ok', 'message' => 'created', 'id' => 'r_1'], 201),
        ]);

        $this->actingAs($admin)
            ->post(route('admin.orders.payments.refund', $payment))
            ->assertRedirect()
            ->assertSessionHas('success', 'Refund request was submited');

        $payment->refresh();
        $this->assertEquals('r_1', $payment->refund_id);
    }


    public function test_order_show_includes_payments_link()
    {
        $role = Role::firstOrCreate(['name' => 'admin']);
        $admin = User::factory()->create();
        $admin->roles()->attach($role->id);

        \Illuminate\Support\Facades\DB::table('countries')->updateOrInsert([
            'iso_alpha2' => 'PT',
        ], [
            'name_pt' => 'Portugal', 'name_en' => 'Portugal', 'country_code' => '351', 'is_active' => true,
        ]);
        $countryId = \Illuminate\Support\Facades\DB::table('countries')->where('iso_alpha2', 'PT')->value('id');
        $user = User::factory()->create();
        $addr = \App\Models\Address::factory()->create(['country_id' => $countryId, 'user_id' => $user->id]);
        $order = Order::factory()->create(['user_id' => $user->id, 'address_id' => $addr->id]);
        $order->update(['order_number' => 'ORD-PAY-BTN']);

        // Ensure an Easypay payload exists so the admin links are rendered
        \App\Models\EasypayPayload::create(['order_id' => $order->id, 'payload' => ['x' => 1]]);

        $this->actingAs($admin)
            ->get(route('admin.orders.show', $order))
            ->assertStatus(200)
            ->assertSee(route('admin.orders.payments.index', ['order_number' => $order->order_number]))
            ->assertSee('Payments');
    }
}
