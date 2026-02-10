<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Order;
use App\Models\EasypayPayment;
use App\Models\User;
use App\Services\EasypayPaymentRefreshService;
use App\Services\EasypayService;
use Mockery;

class EasypayPaymentRefreshServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_marks_order_paid_only_after_authoritative_remote_confirmation()
    {
        $user = User::factory()->create();
        $order = Order::factory()->for($user)->create(['status' => 'WAITING_PAYMENT', 'is_paid' => false]);

        $p = EasypayPayment::create([
            'payment_id' => 'pay_db_paid',
            'order_id' => $order->id,
            'payment_status' => 'paid',
            'paid_at' => now(),
        ]);

        // Without remote confirmation the order must NOT be marked paid — UI may still
        // reflect the persisted payment row and suppress the SDK for final states.
        $svc = app(EasypayPaymentRefreshService::class);
        $res = $svc->refreshLatestPaymentForOrder($order);
        $this->assertTrue($res['suppressSdk']);
        $this->assertEquals('paid', $res['paymentStatus']);
        $this->assertFalse($order->fresh()->is_paid);

        // When Easypay authoritative endpoint confirms 'paid' the order becomes paid
        $single = [ 'id' => 'pay_db_paid', 'payment_status' => 'paid', 'paid_at' => now()->toIso8601String() ];
        $mock = Mockery::mock(EasypayService::class);
        $mock->shouldReceive('getSinglePayment')->with('pay_db_paid')->andReturn($single);
        $this->app->instance(EasypayService::class, $mock);

        $svc = app(EasypayPaymentRefreshService::class);
        $res = $svc->refreshLatestPaymentForOrder($order);

        $this->assertTrue($res['suppressSdk']);
        $this->assertEquals('paid', $res['paymentStatus']);
        $this->assertTrue($order->fresh()->is_paid);
    }

    public function test_pending_db_then_remote_paid_updates_and_marks_order_paid()
    {
        $user = User::factory()->create();
        $order = Order::factory()->for($user)->create(['status' => 'WAITING_PAYMENT', 'is_paid' => false]);

        $p = EasypayPayment::create([
            'payment_id' => 'pay_remote_paid',
            'order_id' => $order->id,
            'payment_status' => 'pending',
        ]);

        $single = [ 'id' => 'pay_remote_paid', 'payment_status' => 'paid', 'paid_at' => now()->toIso8601String() ];

        $mock = Mockery::mock(EasypayService::class);
        $mock->shouldReceive('getSinglePayment')->with('pay_remote_paid')->andReturn($single);
        $this->app->instance(EasypayService::class, $mock);

        $svc = app(EasypayPaymentRefreshService::class);
        $res = $svc->refreshLatestPaymentForOrder($order);

        $this->assertTrue($res['suppressSdk']);
        $this->assertEquals('paid', $res['paymentStatus']);
        $this->assertDatabaseHas('easypay_payments', ['payment_id' => 'pay_remote_paid', 'payment_status' => 'paid']);
        $this->assertTrue($order->fresh()->is_paid);
    }

    public function test_authorised_db_respected_and_not_mark_paid()
    {
        $user = User::factory()->create();
        $order = Order::factory()->for($user)->create(['status' => 'WAITING_PAYMENT', 'is_paid' => false]);

        $p = EasypayPayment::create([
            'payment_id' => 'pay_auth',
            'order_id' => $order->id,
            'payment_status' => 'authorised',
        ]);

        $svc = app(EasypayPaymentRefreshService::class);
        $res = $svc->refreshLatestPaymentForOrder($order);

        $this->assertTrue($res['suppressSdk']);
        $this->assertEquals('authorised', $res['paymentStatus']);
        $this->assertFalse($order->fresh()->is_paid);
    }

    public function test_pending_db_shows_payment_info_and_suppresses_sdk_unless_remote_clears_it()
    {
        $user = User::factory()->create();
        $order = Order::factory()->for($user)->create(['status' => 'WAITING_PAYMENT', 'is_paid' => false]);

        $p = EasypayPayment::create([
            'payment_id' => 'pay_mb',
            'order_id' => $order->id,
            'payment_status' => 'pending',
            'mb_entity' => '111',
            'mb_reference' => '222',
        ]);

        // debug: verify the relation is present in the same process
        file_put_contents('/tmp/epr_debug.json', json_encode([
            'payments_count' => $order->easypayPayments()->count(),
            'latest_id' => $order->easypayPayments()->latest('created_at')->first()?->payment_id,
            'latest_status' => $order->easypayPayments()->latest('created_at')->first()?->payment_status,
        ]));

        // DB-pending should suppress SDK and surface payment info to the user
        $svc = app(EasypayPaymentRefreshService::class);
        $res = $svc->refreshLatestPaymentForOrder($order);

        file_put_contents('/tmp/epr_result.json', json_encode($res));

        $this->assertTrue($res['suppressSdk']);
        $this->assertEquals('pending', $res['paymentStatus']);
        $this->assertNotNull($res['paymentInfo']);
        $this->assertEquals('111', $res['paymentInfo']->mb_entity);

        // If remote reports the payment no longer exists / is canceled, SDK may be allowed
        $mock = Mockery::mock(EasypayService::class);
        $mock->shouldReceive('getSinglePayment')->with('pay_mb')->andReturn(null);
        $this->app->instance(EasypayService::class, $mock);

        $res2 = app(EasypayPaymentRefreshService::class)->refreshLatestPaymentForOrder($order);
        $this->assertFalse($res2['suppressSdk']);
    }
}
