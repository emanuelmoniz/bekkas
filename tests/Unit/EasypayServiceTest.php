<?php

namespace Tests\Unit;

use App\Services\EasypayService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Order;
use App\Models\ShippingTier;
use Tests\TestCase;

class EasypayServiceTest extends TestCase
{
    use RefreshDatabase;
    public function test_get_single_payment_returns_array()
    {
        Http::fake([
            '*' => Http::response(['id' => 'pay_123', 'payment_status' => 'paid'], 200),
        ]);

        $service = new EasypayService;
        $resp = $service->getSinglePayment('pay_123');

        $this->assertIsArray($resp);
        $this->assertEquals('pay_123', $resp['id']);
        $this->assertEquals('paid', $resp['payment_status']);
    }

    public function test_fetch_checkout_returns_ok_body()
    {
        Http::fake([
            '*' => Http::response(['id' => 'checkout_1', 'session' => 'sess_tok'], 200),
        ]);

        $res = EasypayService::fetchCheckout('checkout_1');

        $this->assertTrue($res['ok']);
        $this->assertEquals(200, $res['status']);
        $this->assertIsArray($res['body']);
        $this->assertEquals('checkout_1', $res['body']['id']);
    }

    public function test_build_payload_includes_shipping_item_when_shipping_present()
    {
        Config::set('easypay.enabled', true);

        $tier = ShippingTier::factory()->create(["name_en" => 'Express', 'cost_gross' => 4.5]);

        $order = Order::factory()->hasItems(1)->create([
            'shipping_gross' => 4.50,
            'shipping_tier_name' => 'Express',
            'total_gross' => 12.30 + 4.50,
        ]);

        $payload = EasypayService::buildPayload($order);

        $items = $payload['order']['items'];

        $this->assertTrue(collect($items)->contains(function ($it) use ($tier) {
            return ($it['description'] === 'Express')
                && ($it['quantity'] === 1)
                && ((string) $tier->id === (string) $it['key'])
                && (round(4.5, 2) == $it['value']);
        }));

        $this->assertGreaterThanOrEqual(1, count($items));
    }

    public function test_build_payload_omits_shipping_item_for_free_shipping()
    {
        Config::set('easypay.enabled', true);

        $order = Order::factory()->hasItems(1)->create([
            'shipping_gross' => 0.00,
            'shipping_tier_name' => 'Free tier',
            'total_gross' => 12.30,
        ]);

        $payload = EasypayService::buildPayload($order);

        $items = $payload['order']['items'];

        $this->assertFalse(collect($items)->contains(function ($it) {
            return ($it['description'] === 'Free tier') || ($it['value'] == 0.00);
        }));
    }

    public function test_build_payload_uses_address_phone_when_user_phone_missing()
    {
        Config::set('easypay.enabled', true);

        $address = \App\Models\Address::factory()->create(['phone' => '999888777']);
        $user = \App\Models\User::factory()->create(); // no phone on user

        $order = Order::factory()->hasItems(1)->create([
            'user_id' => $user->id,
            'address_id' => $address->id,
            'total_gross' => 10.00,
        ]);

        $payload = EasypayService::buildPayload($order);

        $this->assertArrayHasKey('customer', $payload);
        $this->assertEquals('999888777', $payload['customer']['phone']);
    }

    public function test_build_payload_sets_empty_phone_when_none_available()
    {
        Config::set('easypay.enabled', true);

        $user = \App\Models\User::factory()->create(); // no phone
        $address = \App\Models\Address::factory()->create(['phone' => null]);

        $order = Order::factory()->hasItems(1)->create([
            'user_id' => $user->id,
            'address_id' => $address->id,
            'total_gross' => 10.00,
        ]);

        $payload = EasypayService::buildPayload($order);

        $this->assertArrayHasKey('customer', $payload);
        $this->assertNull($payload['customer']['phone']);
    }
}
