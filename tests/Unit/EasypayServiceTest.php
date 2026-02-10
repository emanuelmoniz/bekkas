<?php

namespace Tests\Unit;

use App\Services\EasypayService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class EasypayServiceTest extends TestCase
{
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
}
