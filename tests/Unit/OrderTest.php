<?php

namespace Tests\Unit;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_mark_as_paid_by_easypay_queues_customer_email()
    {
        Mail::fake();

        $user = User::factory()->create(['language' => 'en-UK']);
        $order = Order::factory()->for($user)->create(['is_paid' => false, 'status' => 'WAITING_PAYMENT']);

        $ok = $order->markAsPaid('easypay', ['payment_id' => 'p_1']);

        $this->assertTrue($ok);
        $this->assertTrue($order->fresh()->is_paid);

        Mail::assertQueued(\App\Mail\OrderNotification::class, function ($mail) use ($user) {
            return $mail->hasTo($user->email) && ($mail->locale === 'en-UK' || $mail->locale === 'en');
        });
    }
}
