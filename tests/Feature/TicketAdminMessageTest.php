<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Models\Role;
use Illuminate\Support\Facades\Mail;
use App\Mail\TicketNotification;

class TicketAdminMessageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Disable reCAPTCHA during tests so validation does not require external keys
        config(['services.recaptcha.secret_key' => null]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function admin_can_post_a_message_to_a_ticket_and_it_is_stored()
    {
        Mail::fake();

        // Create owner
        $owner = User::factory()->create();

        // Create ticket category and ticket
        $category = TicketCategory::create(['active' => true]);

        $ticket = Ticket::create([
            'user_id' => $owner->id,
            'created_by' => $owner->id,
            'ticket_category_id' => $category->id,
            'title' => 'Test ticket',
            'status' => 'open',
        ]);

        // Create admin user and role
        $role = Role::create(['name' => 'admin']);
        $admin = User::factory()->create();
        $admin->roles()->attach($role->id);

        // Act as admin and post message
        $this->actingAs($admin)
            ->post(route('tickets.messages.store', $ticket), [
                'message' => 'Admin reply',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('ticket_messages', [
            'ticket_id' => $ticket->id,
            'user_id' => $admin->id,
            'message' => 'Admin reply',
        ]);

        // Ensure mails were queued
        Mail::assertQueued(TicketNotification::class);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function message_is_stored_even_if_notification_fails()
    {
        // Simulate mail failing
        Mail::shouldReceive('to')->andThrow(new \Exception('SMTP failure'));

        $owner = User::factory()->create();

        $category = TicketCategory::create(['active' => true]);

        $ticket = Ticket::create([
            'user_id' => $owner->id,
            'created_by' => $owner->id,
            'ticket_category_id' => $category->id,
            'title' => 'Test ticket 2',
            'status' => 'open',
        ]);

        $role = Role::create(['name' => 'admin']);
        $admin = User::factory()->create();
        $admin->roles()->attach($role->id);

        $this->actingAs($admin)
            ->post(route('tickets.messages.store', $ticket), [
                'message' => 'Admin reply despite mail failure',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('ticket_messages', [
            'ticket_id' => $ticket->id,
            'user_id' => $admin->id,
            'message' => 'Admin reply despite mail failure',
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function admin_can_post_without_recaptcha_even_if_secret_is_set()
    {
        // Setup: enable recaptcha in config to simulate production
        config(['services.recaptcha.secret_key' => 'test-key']);

        $owner = User::factory()->create();
        $category = TicketCategory::create(['active' => true]);

        $ticket = Ticket::create([
            'user_id' => $owner->id,
            'created_by' => $owner->id,
            'ticket_category_id' => $category->id,
            'title' => 'Test ticket 3',
            'status' => 'open',
        ]);

        $role = Role::create(['name' => 'admin']);
        $admin = User::factory()->create();
        $admin->roles()->attach($role->id);

        // Admin posts without providing g-recaptcha-response
        $this->actingAs($admin)
            ->post(route('tickets.messages.store', $ticket), [
                'message' => 'Admin reply without recaptcha',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('ticket_messages', [
            'ticket_id' => $ticket->id,
            'user_id' => $admin->id,
            'message' => 'Admin reply without recaptcha',
        ]);
    }
}
