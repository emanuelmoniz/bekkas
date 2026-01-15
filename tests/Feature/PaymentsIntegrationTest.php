<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Country;
use App\Models\Address;
use App\Models\Order;
use App\Services\PaymentService;
use App\Services\PaymentGatewayInterface;
use Tests\Fakes\PaymentGatewayFake;

class PaymentsIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_payment_service_process_marks_order_paid()
    {
        $user = User::factory()->create();
        $country = Country::factory()->create();
        $address = Address::factory()->create(['user_id' => $user->id, 'country_id' => $country->id]);

        $order = \Database\Factories\OrderFactory::new()->for($user)->for($address)->hasItems(1)->create();

        $fake = new PaymentGatewayFake();
        $this->app->instance(PaymentGatewayInterface::class, $fake);

        $service = app(PaymentService::class);

        $res = $service->processPayment($order, ['card' => 'fake']);

        $this->assertEquals('success', $res['status']);
        $this->assertTrue($order->refresh()->is_paid);
    }

    public function test_handle_webhook_validates_and_marks_order_paid()
    {
        $user = User::factory()->create();
        $country = Country::factory()->create();
        $address = Address::factory()->create(['user_id' => $user->id, 'country_id' => $country->id]);

        $order = \Database\Factories\OrderFactory::new()->for($user)->for($address)->hasItems(1)->create();

        $fake = new PaymentGatewayFake();
        $this->app->instance(PaymentGatewayInterface::class, $fake);

        $service = app(PaymentService::class);

        $ok = $service->handleWebhook($order, ['transaction_id' => 'fake-123']);

        $this->assertTrue($ok);
        $this->assertTrue($order->refresh()->is_paid);
    }

    public function test_payment_service_handles_failed_response()
    {
        $user = User::factory()->create();
        $country = Country::factory()->create();
        $address = Address::factory()->create(['user_id' => $user->id, 'country_id' => $country->id]);

        $order = \Database\Factories\OrderFactory::new()->for($user)->for($address)->hasItems(1)->create();

        $fake = new PaymentGatewayFake();
        $fake->nextResponse = ['status' => 'failed', 'transaction_id' => 'fail-1'];
        $this->app->instance(PaymentGatewayInterface::class, $fake);

        $service = app(PaymentService::class);
        $res = $service->processPayment($order, ['card' => 'fake']);

        $this->assertEquals('failed', $res['status']);
        $this->assertFalse($order->refresh()->is_paid);
    }
}
