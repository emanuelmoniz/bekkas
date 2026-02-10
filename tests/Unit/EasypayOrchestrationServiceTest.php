<?php

namespace Tests\Unit;

use App\Models\EasypayCheckoutSession;
use App\Models\Order;
use App\Models\User;
use App\Services\EasypayOrchestrationService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EasypayOrchestrationServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_is_session_fresh_respects_ttl()
    {
        $user = User::factory()->create();
        $order = Order::factory()->for($user)->create(['status' => 'WAITING_PAYMENT', 'is_paid' => false]);

        $s = EasypayCheckoutSession::create([
            'order_id' => $order->id,
            'is_active' => true,
            'status' => 'pending',
            'message' => json_encode([]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // under TTL => fresh
        $s->updated_at = Carbon::now()->subSeconds(100);
        $s->save();
        $this->assertTrue(EasypayOrchestrationService::isSessionFresh($s, 1800));

        // exactly TTL => not fresh
        $s->updated_at = Carbon::now()->subSeconds(1800);
        $s->save();
        $this->assertFalse(EasypayOrchestrationService::isSessionFresh($s, 1800));

        // older than TTL => not fresh
        $s->updated_at = Carbon::now()->subSeconds(3600);
        $s->save();
        $this->assertFalse(EasypayOrchestrationService::isSessionFresh($s, 1800));
    }

    public function test_get_latest_active_manifest_respects_ttl_and_parses_message()
    {
        $user = User::factory()->create();
        $order = Order::factory()->for($user)->create(['status' => 'WAITING_PAYMENT', 'is_paid' => false]);

        // make the "fresh" session clearly inside small TTL windows for deterministic asserts
        // Create a fresh session that includes canonical DB fields (the manifest is now
        // derived from `checkout_id`/`session_id`, not from `message`).
        $fresh = EasypayCheckoutSession::create([
            'order_id' => $order->id,
            'checkout_id' => 'c1',
            'session_id' => 's1',
            'is_active' => true,
            'status' => 'pending',
            'message' => json_encode(['checkout' => ['id' => 'c1']]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        // persist a deterministic past updated_at via update() to avoid DB-side overrides
        $fresh->update(['updated_at' => Carbon::now()->subSeconds(10)]);
        $fresh->refresh();

        $old = EasypayCheckoutSession::create([
            'order_id' => $order->id,
            'checkout_id' => 'c-old',
            'session_id' => 's-old',
            'is_active' => true,
            'status' => 'pending',
            'message' => json_encode(['checkout' => ['id' => 'c-old']]),
            'created_at' => now()->subSeconds(3601),
            'updated_at' => Carbon::now()->subSeconds(3600),
        ]);

        $orch = new EasypayOrchestrationService;

        $manifest = $orch->getLatestActiveManifest($order, 1800);
        $this->assertIsArray($manifest);
        $this->assertEquals('c1', $manifest['id']);
        $this->assertEquals('s1', $manifest['session']);

        // small TTL that still includes the fresh session (behaviour already covered by isSessionFresh tests)
        $manifest2 = $orch->getLatestActiveManifest($order, 30);
        $this->assertIsArray($manifest2);
        $this->assertEquals('c1', $manifest2['id']);

        // if the latest session is not active (or not pending) we should get null
        $fresh->is_active = false;
        $fresh->save();
        $manifest3 = $orch->getLatestActiveManifest($order, 1800);
        $this->assertNull($manifest3);
    }

    public function test_prepareSdkForOrder_cancels_active_session_persists_checkout_details_and_recreates_session()
    {
        \Illuminate\Support\Facades\Config::set('easypay.enabled', true);
        \Illuminate\Support\Facades\Config::set('easypay.base_url', 'https://api.test.easypay.pt/2.0');

        \Illuminate\Support\Facades\Http::fake([
            'https://api.test.easypay.pt/2.0/checkout/chk_live' => \Illuminate\Support\Facades\Http::response(['status' => 'success', 'checkout' => ['id' => 'chk_live', 'status' => 'success'], 'payment' => ['methods' => ['mb'], 'type' => 'single'], 'value' => 15.00], 200),
            'https://api.test.easypay.pt/2.0/single/pay_pending' => \Illuminate\Support\Facades\Http::response(['id' => 'pay_pending', 'payment_status' => 'pending'], 200),
            'https://api.test.easypay.pt/2.0/checkout' => \Illuminate\Support\Facades\Http::response(['id' => 'chk_new', 'session' => 'sess'], 201),
        ]);

        $user = \App\Models\User::factory()->create();
        $order = \App\Models\Order::factory()->for($user)->create(['status' => 'WAITING_PAYMENT', 'is_paid' => false]);

        $payload = \App\Models\EasypayPayload::create(['order_id' => $order->id, 'payload' => ['x' => 1]]);
        $active = \App\Models\EasypayCheckoutSession::create(['order_id' => $order->id, 'payload_id' => $payload->id, 'checkout_id' => 'chk_live', 'status' => 'pending', 'is_active' => true]);
        \App\Models\EasypayPayment::create(['order_id' => $order->id, 'payment_id' => 'pay_pending', 'payment_status' => 'pending']);

        $orch = new \App\Services\EasypayOrchestrationService;
        $res = $orch->prepareSdkForOrder($order);

        $this->assertEquals('new-manifest', $res['action']);
        $this->assertArrayHasKey('manifest', $res);

        $s = \App\Models\EasypayCheckoutSession::where('checkout_id', 'chk_live')->first();
        $this->assertNotNull($s->message);
        $this->assertStringContainsString('"checkout"', $s->message);
        $this->assertStringContainsString('"status"', $s->message);
        $this->assertEquals('canceled', $s->status);

        $this->assertDatabaseHas('easypay_checkout_sessions', ['checkout_id' => 'chk_new']);
    }
}
