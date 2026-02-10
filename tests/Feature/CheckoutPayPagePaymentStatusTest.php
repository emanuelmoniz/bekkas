<?php

namespace Tests\Feature;

use App\Models\EasypayPayment;
use App\Models\Order;
use App\Models\User;
use App\Services\EasypayService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Mockery;
use Tests\TestCase;

class CheckoutPayPagePaymentStatusTest extends TestCase
{
    use RefreshDatabase;

    public function test_paid_latest_payment_shows_paid_message_and_hides_sdk_and_marks_order_paid()
    {
        Config::set('easypay.enabled', true);
        putenv('EASYPAY_SDK_URL=https://sdk.test/easypay.js');

        $user = User::factory()->create();
        $order = Order::factory()->for($user)->create(['status' => 'WAITING_PAYMENT', 'is_paid' => false]);

        $p = EasypayPayment::create([
            'payment_id' => 'pay_paid_1',
            'order_id' => $order->id,
            'payment_status' => 'paid',
            'paid_at' => now(),
        ]);

        // sanity checks: DB preconditions
        $this->assertEquals(1, $order->easypayPayments()->count());
        $this->assertEquals('paid', $p->payment_status);

        // Without remote confirmation we must NOT mark the order as paid
        $this->actingAs($user);
        $ctrl = app(\App\Http\Controllers\OrderController::class);
        $view = $ctrl->pay($order);
        $html = $view->render();
        $this->assertFalse($order->fresh()->is_paid);

        // When the authoritative Easypay endpoint confirms 'paid' the controller should mark the order
        $single = ['id' => 'pay_paid_1', 'payment_status' => 'paid', 'paid_at' => now()->toIso8601String()];
        $mock = Mockery::mock(EasypayService::class);
        $mock->shouldReceive('getSinglePayment')->with('pay_paid_1')->andReturn($single);
        $this->app->instance(EasypayService::class, $mock);

        $view = $ctrl->pay($order);
        $html = $view->render();
        $this->assertTrue(str_contains($html, 'Payment completed') || str_contains($html, 'Pagamento concluído'));
        $this->assertStringNotContainsString('id="easypay-manifest"', $html);
        $this->assertStringNotContainsString('id="easypay-checkout"', $html);

        $this->assertDatabaseHas('easypay_payments', [
            'payment_id' => 'pay_paid_1',
            'payment_status' => 'paid',
        ]);

        $this->assertTrue($order->fresh()->is_paid, 'Order should be marked as paid only after Easypay confirms paid');
        $this->assertEquals('PROCESSING', $order->fresh()->status);
    }

    public function test_authorised_latest_payment_shows_authorised_message_and_hides_sdk_but_does_not_mark_paid()
    {
        Config::set('easypay.enabled', true);
        putenv('EASYPAY_SDK_URL=https://sdk.test/easypay.js');

        $user = User::factory()->create();
        $order = Order::factory()->for($user)->create(['status' => 'WAITING_PAYMENT', 'is_paid' => false]);

        // seed DB as authorised — pay page should respect DB state and hide SDK
        $p = EasypayPayment::create([
            'payment_id' => 'pay_auth_1',
            'order_id' => $order->id,
            'payment_status' => 'authorised',
        ]);

        // sanity checks: DB preconditions
        $this->assertEquals(1, $order->easypayPayments()->count());
        $this->assertEquals('authorised', $p->payment_status);

        $this->actingAs($user);
        $ctrl = app(\App\Http\Controllers\OrderController::class);
        $view = $ctrl->pay($order);
        $html = $view->render();

        $this->assertTrue(str_contains($html, 'Payment authorised') || str_contains($html, 'Pagamento autorizado'));

        // SDK must NOT be rendered for authorised
        $this->assertStringNotContainsString('id="easypay-manifest"', $html);
        $this->assertStringNotContainsString('id="easypay-checkout"', $html);

        $this->assertDatabaseHas('easypay_payments', [
            'payment_id' => 'pay_auth_1',
            'payment_status' => 'authorised',
        ]);

        $this->assertFalse($order->fresh()->is_paid, 'Authorised should not mark order as paid');
    }

    public function test_pending_payment_shows_payment_information_and_suppresses_sdk_even_if_manifest_exists()
    {
        Config::set('easypay.enabled', true);
        putenv('EASYPAY_SDK_URL=https://sdk.test/easypay.js');

        $user = User::factory()->create();
        $order = Order::factory()->for($user)->create(['status' => 'WAITING_PAYMENT', 'is_paid' => false]);

        $p = EasypayPayment::create([
            'payment_id' => 'pay_mb_1',
            'order_id' => $order->id,
            'payment_status' => 'pending',
            'mb_entity' => '12345',
            'mb_reference' => '987654321',
            'mb_expiration_time' => now()->addDays(2),
            'iban' => 'PT50000201231234567890154',
        ]);

        // Simulate an existing active manifest/session for this order (real-world race)
        \App\Models\EasypayCheckoutSession::create([
            'order_id' => $order->id,
            'payload_id' => null,
            'checkout_id' => 'chk-cypress-1',
            'is_active' => true,
            'status' => 'pending',
            'message' => json_encode(['id' => 'm-cypress-1', 'session' => 's1']),
        ]);

        // sanity checks: DB preconditions
        $this->assertEquals(1, $order->easypayPayments()->count());
        $this->assertEquals('pending', $p->payment_status);

        $this->actingAs($user);
        $ctrl = app(\App\Http\Controllers\OrderController::class);
        $view = $ctrl->pay($order);
        $html = $view->render();

        // UI shows payment info
        $this->assertTrue(str_contains($html, 'Payment information') || str_contains($html, 'Informação de pagamento'));
        $this->assertStringContainsString('12345', $html);
        $this->assertStringContainsString('987654321', $html);

        // SDK and manifest must NOT be present when a persisted pending payment exists
        $this->assertStringNotContainsString('id="easypay-manifest"', $html);
        $this->assertStringNotContainsString('id="easypay-checkout"', $html);

        // The SDK loader text must also NOT be rendered (localized or fallback)
        $this->assertStringNotContainsString(t('checkout.pay.loading_widget') ?: 'Loading payment widget…', $html);
        $this->assertStringNotContainsString('A carregar o componente de pagamento', $html);
    }

    /**
     * Regression: defensive runtime guard — if the server-side view contains both
     * an Easypay `activeManifest` and a persisted `paymentInfo` with status
     * `pending`, the SDK MUST NOT initialise. This test simulates the mismatch
     * (possible cache/race) and asserts the page includes the runtime guard.
     */
    public function test_runtime_guard_prevents_sdk_when_paymentinfo_is_pending_even_if_manifest_present()
    {
        Config::set('easypay.enabled', true);
        putenv('EASYPAY_SDK_URL=https://sdk.test/easypay.js');

        $user = User::factory()->create();
        $order = Order::factory()->for($user)->create(['status' => 'WAITING_PAYMENT', 'is_paid' => false]);

        // Craft a persisted pending payment (the UI should still show payment info)
        $p = EasypayPayment::create([
            'payment_id' => 'pay_mb_runtime',
            'order_id' => $order->id,
            'payment_status' => 'pending',
            'mb_entity' => '555',
            'mb_reference' => '666',
        ]);

        // Simulate an orchestra bug / cache that left a manifest present
        $fakeManifest = ['id' => 'm-runtime', 'session' => 's-runtime'];

        // Render the view **with** the manifest but **without** suppressing the SDK
        // (this reproduces the bad-state we want the runtime guard to handle).
        $this->actingAs($user);
        $html = view('orders.pay', [
            'order' => $order,
            'payload' => null,
            'sessions' => collect(),
            'activeManifest' => $fakeManifest,
            'suppressSdk' => false,
            'paymentInfo' => $p,
            'paymentStatus' => 'pending',
        ])->render();

        // The page may either (A) include a manifest *and* a runtime guard, or
        // (B) server-side hide the manifest entirely when a persisted payment is
        // pending — both are acceptable and protected behaviors. Always assert
        // the loader text is NOT present.
        $this->assertStringNotContainsString(t('checkout.pay.loading_widget') ?: 'Loading payment widget…', $html);
        $this->assertStringNotContainsString('A carregar o componente de pagamento', $html);

        // Either the manifest is absent (server suppressed it) OR it's present but
        // guarded by the runtime check — assert one of those is true.
        $manifestPresent = str_contains($html, 'easypay-manifest');
        $guardPresent = str_contains($html, "paymentInfo.payment_status === 'pending'") || str_contains($html, 'runtime guard');
        $this->assertTrue(! $manifestPresent || $guardPresent, 'Either manifest must be absent or a runtime guard must be present.');
    }

    public function test_pay_page_refreshes_latest_payment_from_easypay_and_updates_db()
    {
        Config::set('easypay.enabled', true);
        putenv('EASYPAY_SDK_URL=https://sdk.test/easypay.js');

        $user = User::factory()->create();
        $order = Order::factory()->for($user)->create(['status' => 'WAITING_PAYMENT', 'is_paid' => false]);

        $p = EasypayPayment::create([
            'payment_id' => 'pay_remote_1',
            'order_id' => $order->id,
            'payment_status' => 'pending',
        ]);

        $single = [
            'id' => 'pay_remote_1',
            'payment_status' => 'paid',
            'paid_at' => now()->toIso8601String(),
        ];

        $mock = Mockery::mock(EasypayService::class);
        $mock->shouldReceive('getSinglePayment')->once()->with('pay_remote_1')->andReturn($single);
        $this->app->instance(EasypayService::class, $mock);

        // call controller directly so the mocked service is resolved in the same process
        $this->actingAs($user);
        $ctrl = app(\App\Http\Controllers\OrderController::class);
        $ctrl->pay($order);

        $this->assertDatabaseHas('easypay_payments', ['payment_id' => 'pay_remote_1', 'payment_status' => 'paid']);
        $this->assertTrue($order->fresh()->is_paid);
    }

    public function test_direct_controller_pay_respects_db_payment_status()
    {
        Config::set('easypay.enabled', true);

        $user = User::factory()->create();
        $order = Order::factory()->for($user)->create(['status' => 'WAITING_PAYMENT', 'is_paid' => false]);

        $p = EasypayPayment::create([
            'payment_id' => 'pay_direct_1',
            'order_id' => $order->id,
            'payment_status' => 'paid',
            'paid_at' => now(),
        ]);

        $ctrl = app(\App\Http\Controllers\OrderController::class);
        $this->actingAs($user);
        $view = $ctrl->pay($order);

        $this->assertInstanceOf(\Illuminate\View\View::class, $view);
        $data = $view->getData();
        $this->assertTrue(data_get($data, 'suppressSdk') === true);
        $this->assertEquals('paid', data_get($data, 'paymentStatus'));
    }
}
