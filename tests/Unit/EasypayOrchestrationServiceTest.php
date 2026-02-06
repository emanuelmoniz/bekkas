<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Order;
use App\Models\EasypayCheckoutSession;
use App\Models\User;
use App\Models\Product;
use App\Services\EasypayOrchestrationService;
use Carbon\Carbon;

class EasypayOrchestrationServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_is_session_fresh_respects_ttl()
    {
        $user = User::factory()->create();
        $order = Order::factory()->for($user)->create([ 'status' => 'WAITING_PAYMENT', 'is_paid' => false ]);

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
        $order = Order::factory()->for($user)->create([ 'status' => 'WAITING_PAYMENT', 'is_paid' => false ]);

        // make the "fresh" session clearly inside small TTL windows for deterministic asserts
        $fresh = EasypayCheckoutSession::create([
            'order_id' => $order->id,
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
            'is_active' => true,
            'status' => 'pending',
            'message' => json_encode(['checkout' => ['id' => 'c-old']]),
            'created_at' => now()->subSeconds(3601),
            'updated_at' => Carbon::now()->subSeconds(3600),
        ]);

        $orch = new EasypayOrchestrationService();

        $manifest = $orch->getLatestActiveManifest($order, 1800);
        $this->assertIsArray($manifest);
        $this->assertEquals('c1', $manifest['checkout']['id']);

        // small TTL that still includes the fresh session (behaviour already covered by isSessionFresh tests)
        $manifest2 = $orch->getLatestActiveManifest($order, 30);
        $this->assertIsArray($manifest2);

        // if the latest session is not active (or not pending) we should get null
        $fresh->is_active = false;
        $fresh->save();
        $manifest3 = $orch->getLatestActiveManifest($order, 1800);
        $this->assertNull($manifest3);
    }
}
