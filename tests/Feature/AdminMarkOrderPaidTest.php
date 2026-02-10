<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Order;

class AdminMarkOrderPaidTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_manually_mark_order_as_paid()
    {
        $role = \App\Models\Role::firstOrCreate(['name' => 'admin']);
        $admin = User::factory()->create();
        $admin->roles()->attach($role->id);

        $user = User::factory()->create();
        $order = Order::factory()->for($user)->create(['status' => 'WAITING_PAYMENT', 'is_paid' => false]);

        $this->actingAs($admin);

        $resp = $this->patch(route('admin.orders.update', $order), [
            'status' => $order->status,
            'is_paid' => true,
        ]);

        $resp->assertRedirect(route('admin.orders.show', $order));
        $this->assertTrue($order->fresh()->is_paid, 'Admin update should mark order as paid');
        $this->assertEquals('PROCESSING', $order->fresh()->status);
    }
}
