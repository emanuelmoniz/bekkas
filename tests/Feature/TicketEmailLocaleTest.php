<?php

namespace Tests\Feature;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class TicketEmailLocaleTest extends TestCase
{
    use RefreshDatabase;

    public function test_ticket_notifications_respect_recipient_locale_and_admins_get_english()
    {
        Mail::fake();

        $owner = User::factory()->create(['language' => 'pt-PT']);
        $admin = User::factory()->create();
        $admin->roles()->attach(\App\Models\Role::firstOrCreate(['name' => 'admin'])->id);

        // Create a minimal ticket (no Ticket factory available in this repo)
        $ticket = \App\Models\Ticket::create([
            'user_id' => $owner->id,
            'created_by' => $owner->id,
            'ticket_category_id' => \App\Models\TicketCategory::firstOrCreate(['slug' => 'general'])->id,
            'title' => 'Help',
            'status' => 'open',
            'opened_at' => now(),
            'last_message_at' => now(),
            'read_state' => [$owner->id => now()],
            'uuid' => (string) \Illuminate\Support\Str::uuid(),
        ]);

        $message = \App\Models\TicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => $owner->id,
            'message' => 'Hello',
        ]);

        // Notify participants (recipient set includes owner + admins). Use an actor id
        // different from participants so both owner and admin will be notified in the test.
        $ticket->notifyParticipants($message, 'tickets.email.event.new_message', 0);

        // Owner should receive mail in Portuguese and the email must include the friendly ticket GUID
        Mail::assertQueued(\App\Mail\TicketNotification::class, function ($mail) use ($owner, $ticket) {
            $html = method_exists($mail, 'render') ? $mail->render() : '';

            return $mail->hasTo($owner->email)
                && ($mail->locale === 'pt-PT' || $mail->locale === 'pt')
                && str_contains($html, $ticket->ticket_number);
        });

        // Admin should receive mail in English and include ticket GUID
        Mail::assertQueued(\App\Mail\TicketNotification::class, function ($mail) use ($admin, $ticket) {
            $html = method_exists($mail, 'render') ? $mail->render() : '';

            return $mail->hasTo($admin->email)
                && ($mail->locale === 'en-UK' || $mail->locale === 'en')
                && str_contains($html, $ticket->ticket_number);
        });
    }
}
