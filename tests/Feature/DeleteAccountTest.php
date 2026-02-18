<?php

namespace Tests\Feature;

use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

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
}
