<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Country;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderUuidTest extends TestCase
{
    use RefreshDatabase;

    public function test_order_urls_use_uuid()
    {
        $user = User::factory()->create();

        $country = Country::firstOrCreate(['iso_alpha2' => 'PT'], [
            'name_pt' => 'Portugal',
            'name_en' => 'Portugal',
            'country_code' => '351',
        ]);

        $address = Address::create([
            'user_id' => $user->id,
            'title' => 'Home',
            'address_line_1' => 'Rua Teste',
            'postal_code' => '1000-000',
            'city' => 'Lisbon',
            'country_id' => $country->id,
        ]);

        $order = Order::create([
            'user_id' => $user->id,
            'address_id' => $address->id,
            'address_title' => $address->title,
            'address_nif' => $address->nif ?? null,
            'address_line_1' => $address->address_line_1,
            'address_line_2' => $address->address_line_2 ?? null,
            'address_postal_code' => $address->postal_code,
            'address_city' => $address->city,
            'address_country' => $country->name_en,
            'products_total_net' => 10,
            'products_total_tax' => 2.3,
            'products_total_gross' => 12.3,
            'shipping_net' => 0,
            'shipping_tax' => 0,
            'shipping_gross' => 0,
            'total_net' => 10,
            'total_tax' => 2.3,
            'total_gross' => 12.3,
        ]);

        $url = route('orders.show', $order);

        $this->assertStringContainsString($order->uuid, $url);

        $path = parse_url($url, PHP_URL_PATH);
        $segments = array_values(array_filter(explode('/', $path)));
        $last = end($segments);
        $this->assertEquals($order->uuid, $last);
        $this->assertNotEquals((string) $order->id, $last);
    }

    public function test_view_order_by_uuid_returns_ok()
    {
        $user = User::factory()->create();

        $country = Country::firstOrCreate(['iso_alpha2' => 'PT'], [
            'name_pt' => 'Portugal',
            'name_en' => 'Portugal',
            'country_code' => '351',
        ]);

        $address = Address::create([
            'user_id' => $user->id,
            'title' => 'Home',
            'address_line_1' => 'Rua Teste',
            'postal_code' => '1000-000',
            'city' => 'Lisbon',
            'country_id' => $country->id,
        ]);

        $order = Order::create([
            'user_id' => $user->id,
            'address_id' => $address->id,
            'address_title' => $address->title,
            'address_nif' => $address->nif ?? null,
            'address_line_1' => $address->address_line_1,
            'address_line_2' => $address->address_line_2 ?? null,
            'address_postal_code' => $address->postal_code,
            'address_city' => $address->city,
            'address_country' => $country->name_en,
            'products_total_net' => 10,
            'products_total_tax' => 2.3,
            'products_total_gross' => 12.3,
            'shipping_net' => 0,
            'shipping_tax' => 0,
            'shipping_gross' => 0,
            'total_net' => 10,
            'total_tax' => 2.3,
            'total_gross' => 12.3,
        ]);

        $this->actingAs($user)
            ->get(route('orders.show', $order))
            ->assertOk();
    }
}
