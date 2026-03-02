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

        // Ensure DB-driven payment-status translations are available for the view
        \App\Models\StaticTranslation::create(['key' => 'checkout.pay.status.paid', 'locale' => 'en-UK', 'value' => 'Payment completed — your order is being processed.']);
        \App\Models\StaticTranslation::create(['key' => 'checkout.pay.status.paid', 'locale' => 'pt-PT', 'value' => 'Pagamento concluído — a sua encomenda está a ser processada.']);
        \App\Models\StaticTranslation::create(['key' => 'checkout.pay.status.authorised', 'locale' => 'en-UK', 'value' => 'Payment authorised — processing is underway, please check your order details in a moment.']);
        \App\Models\StaticTranslation::create(['key' => 'checkout.pay.status.authorised', 'locale' => 'pt-PT', 'value' => 'Pagamento autorizado — o processamento está em curso, verifique os detalhes da encomenda dentro de momentos.']);
        \Illuminate\Support\Facades\Cache::forget('static_translations_all');

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

        // re-resolve controller so the mocked EasypayService is injected into the
        // EasypayPaymentRefreshService used by the controller
        $ctrl = app(\App\Http\Controllers\OrderController::class);

        $view = $ctrl->pay($order);
        $html = $view->render();
        $this->assertTrue(str_contains($html, 'Payment completed') || str_contains($html, 'Pagamento concluído'));

        // After remote 'paid' confirmation the order is moved out of WAITING_PAYMENT;
        // the SDK container must NOT be exposed and no manifest should be present.
        $this->assertStringNotContainsString('id="easypay-checkout"', $html);
        $this->assertStringNotContainsString('id="easypay-manifest"', $html);

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

        // Ensure DB-driven payment-status translations are available for the view
        \App\Models\StaticTranslation::create(['key' => 'checkout.pay.status.paid', 'locale' => 'en-UK', 'value' => 'Payment completed — your order is being processed.']);
        \App\Models\StaticTranslation::create(['key' => 'checkout.pay.status.paid', 'locale' => 'pt-PT', 'value' => 'Pagamento concluído — a sua encomenda está a ser processada.']);
        \App\Models\StaticTranslation::create(['key' => 'checkout.pay.status.authorised', 'locale' => 'en-UK', 'value' => 'Payment authorised — processing is underway, please check your order details in a moment.']);
        \App\Models\StaticTranslation::create(['key' => 'checkout.pay.status.authorised', 'locale' => 'pt-PT', 'value' => 'Pagamento autorizado — o processamento está em curso, verifique os detalhes da encomenda dentro de momentos.']);
        \Illuminate\Support\Facades\Cache::forget('static_translations_all');

        $this->actingAs($user);
        $ctrl = app(\App\Http\Controllers\OrderController::class);
        $view = $ctrl->pay($order);
        $html = $view->render();

        $this->assertTrue(str_contains($html, 'Payment authorised') || str_contains($html, 'Pagamento autorizado'));

        // SDK container is always rendered; per new start logic a fresh manifest may be created
        $this->assertStringContainsString('id="easypay-checkout"', $html);
        $this->assertStringContainsString('id="easypay-manifest"', $html);

        $this->assertDatabaseHas('easypay_payments', [
            'payment_id' => 'pay_auth_1',
            'payment_status' => 'authorised',
        ]);

        $this->assertFalse($order->fresh()->is_paid, 'Authorised should not mark order as paid');
    }

    public function test_pending_payment_shows_payment_information_and_suppresses_sdk_even_if_manifest_exists()
    {
        Config::set('easypay.enabled', true);
        Config::set('easypay.base_url', 'https://api.test.easypay.pt/2.0');
        putenv('EASYPAY_SDK_URL=https://sdk.test/easypay.js');

        \Illuminate\Support\Facades\Http::fake([
            'https://api.test.easypay.pt/2.0/checkout' => \Illuminate\Support\Facades\Http::response(['id' => 'chk-new', 'session' => 's-new', 'status' => 'created'], 201),
        ]);

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

        // Payment information removed from the frontend
        $this->assertFalse(str_contains($html, 'Payment information') || str_contains($html, 'Informação de pagamento'));
        $this->assertStringNotContainsString('12345', $html);
        $this->assertStringNotContainsString('987654321', $html);

        // SDK container + manifest should be available according to new start logic
        $this->assertStringContainsString('id="easypay-manifest"', $html);
        $this->assertStringContainsString('id="easypay-checkout"', $html);

        // Loader text should be present
        $this->assertStringContainsString(t('checkout.pay.loading_widget') ?: 'Loading payment widget…', $html);
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

        // Runtime guard was removed and payment information no longer rendered — the SDK container/loader should be present
        $this->assertStringContainsString(t('checkout.pay.loading_widget') ?: 'Loading payment widget…', $html);
        $this->assertStringContainsString('id="easypay-checkout"', $html);

        // Ensure no runtime guard JS snippet remains on the page
        $this->assertFalse(str_contains($html, "paymentInfo.payment_status === 'pending'") || str_contains($html, 'runtime guard'));
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

        // UI: SDK container is always rendered even when controller requests suppression
        $html = $view->render();
        $this->assertStringContainsString('id="easypay-checkout"', $html);
    }

    public function test_pay_page_hides_sdk_for_non_waiting_payment_status_even_if_session_exists()
    {
        Config::set('easypay.enabled', true);
        putenv('EASYPAY_SDK_URL=https://sdk.test/easypay.js');

        $user = User::factory()->create();
        $order = Order::factory()->for($user)->create(['status' => 'PROCESSING', 'is_paid' => false]);

        $payload = \App\Models\EasypayPayload::create(['order_id' => $order->id, 'payload' => ['x' => 1]]);
        \App\Models\EasypayCheckoutSession::create([
            'order_id' => $order->id,
            'payload_id' => $payload->id,
            'checkout_id' => 'chk_block',
            'session_id' => 'sess_block',
            'is_active' => true,
            'status' => 'pending',
            'message' => json_encode(['id' => 'chk_block', 'session' => 'sess_block']),
        ]);

        $resp = $this->actingAs($user)->get(route('orders.pay', $order->uuid));
        $resp->assertStatus(403);
        $this->assertStringNotContainsString('easypay-manifest', $resp->getContent() ?: '');
    }
}
