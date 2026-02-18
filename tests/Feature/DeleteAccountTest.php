<?php

namespace Tests\Feature;

use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Notifications\DeleteAccountNotification;

class DeleteAccountTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_with_tickets_referenced_as_creator_can_delete_account()
    {
        $user = User::factory()->create();
        $other = User::factory()->create();

        $cat = TicketCategory::factory()->create();

        // Ticket owned by $other but created_by = $user (edge case)
        $ticket = Ticket::create([
            'user_id' => $other->id,
            'created_by' => $user->id,
            'ticket_category_id' => $cat->id,
            'title' => 'Help',
            'opened_at' => now(),
        ]);

        $this->actingAs($user)->delete(route('profile.destroy'), ['password' => 'password'])
            ->assertRedirect('/');

        $this->assertDatabaseMissing('users', ['id' => $user->id]);

        // Ticket should still exist and `created_by` must now reference the ticket owner
        $this->assertDatabaseHas('tickets', ['id' => $ticket->id, 'created_by' => $other->id]);
    }

    public function test_user_who_created_own_ticket_can_delete_account()
    {
        $user = User::factory()->create(['password' => bcrypt('password')]);
        $cat = TicketCategory::factory()->create();

        $ticket = Ticket::create([
            'user_id' => $user->id,
            'created_by' => $user->id,
            'ticket_category_id' => $cat->id,
            'title' => 'My ticket',
            'opened_at' => now(),
        ]);

        $this->actingAs($user)->delete(route('profile.destroy'), ['password' => 'password'])
            ->assertRedirect('/');

        $this->assertDatabaseMissing('users', ['id' => $user->id]);

        // Ticket owner was the deleted user — the ticket is cascade-deleted by DB.
        $this->assertDatabaseMissing('tickets', ['id' => $ticket->id]);
    }

    public function test_social_only_user_can_request_deletion_link_and_confirm_via_signed_url()
    {
        Notification::fake();

        $user = User::factory()->create(['email' => 'social@example.com']);
        $user->socialAccounts()->create(['provider' => 'google', 'provider_id' => '12345']);

        $this->actingAs($user)
            ->post(route('profile.delete.request'))
            ->assertRedirect()
            ->assertSessionHas('status', 'deletion-link-sent');

        Notification::assertSentTo($user, DeleteAccountNotification::class);

        $signed = URL::temporarySignedRoute('profile.delete.confirm', now()->addMinutes(config('auth.verification.expire', 60)), [
            'id' => $user->id,
            'hash' => sha1($user->getEmailForVerification()),
        ]);

        // GET must show a confirmation page (do not delete on GET)
        $this->get($signed)->assertStatus(200)->assertSee(t('profile.delete_by_email_subject') ?: 'Confirm account deletion');

        // perform deletion via POST (form submission)
        $this->post($signed)->assertRedirect('/');

        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_signed_deletion_link_requires_valid_signature()
    {
        $user = User::factory()->create(['email' => 'social2@example.com']);
        $user->socialAccounts()->create(['provider' => 'google', 'provider_id' => '67890']);

        $expired = URL::temporarySignedRoute('profile.delete.confirm', now()->subMinutes(1), [
            'id' => $user->id,
            'hash' => sha1($user->getEmailForVerification()),
        ]);

        $this->get($expired)->assertStatus(403);

        $this->assertDatabaseHas('users', ['id' => $user->id]);
    }

    public function test_signed_deletion_link_deletes_user_when_visited_while_logged_out()
    {
        $user = User::factory()->create(['email' => 'social3@example.com']);
        $user->socialAccounts()->create(['provider' => 'google', 'provider_id' => 'abcde']);

        $signed = URL::temporarySignedRoute('profile.delete.confirm', now()->addMinutes(config('auth.verification.expire', 60)), [
            'id' => $user->id,
            'hash' => sha1($user->getEmailForVerification()),
        ]);

        // GET shows confirmation page; deletion is performed on POST to prevent scanners from deleting
        $this->get($signed)->assertStatus(200)->assertSee(t('profile.delete_by_email_subject') ?: 'Confirm account deletion');

        $this->post($signed)->assertRedirect('/');

        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }
}
