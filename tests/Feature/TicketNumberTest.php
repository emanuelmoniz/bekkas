<?php

namespace Tests\Feature;

use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketNumberTest extends TestCase
{
    use RefreshDatabase;

    public function test_ticket_number_is_generated_and_has_expected_format()
    {
        $owner = User::factory()->create();
        $category = TicketCategory::create(['active' => true]);

        $ticket = Ticket::create([
            'user_id' => $owner->id,
            'created_by' => $owner->id,
            'ticket_category_id' => $category->id,
            'title' => 'GUID format test',
            'status' => 'open',
        ]);

        $this->assertNotEmpty($ticket->ticket_number);
        $this->assertMatchesRegularExpression('/^TCK-[A-Z0-9]{4}-[A-Z0-9]{4}$/', $ticket->ticket_number);
    }

    public function test_ticket_number_is_visible_on_client_and_admin_pages_and_searchable()
    {
        $owner = User::factory()->create();
        $admin = User::factory()->create();
        $admin->roles()->attach(\App\Models\Role::firstOrCreate(['name' => 'admin'])->id);

        $category = TicketCategory::create(['active' => true]);

        $ticket = Ticket::create([
            'user_id' => $owner->id,
            'created_by' => $owner->id,
            'ticket_category_id' => $category->id,
            'title' => 'Visibility test',
            'status' => 'open',
        ]);

        // Client index/show
        $this->actingAs($owner)
            ->get(route('tickets.index'))
            ->assertOk()
            ->assertSee($ticket->ticket_number);

        $this->actingAs($owner)
            ->get(route('tickets.show', $ticket))
            ->assertOk()
            ->assertSee($ticket->ticket_number);

        // Admin index/show and searchable by ticket_number
        $this->actingAs($admin)
            ->get(route('admin.tickets.index'))
            ->assertOk()
            ->assertSee($ticket->ticket_number);

        $this->actingAs($admin)
            ->get(route('admin.tickets.show', $ticket))
            ->assertOk()
            ->assertSee($ticket->ticket_number);

        // Search by ticket_number
        $this->actingAs($admin)
            ->get(route('admin.tickets.index', ['ticket_id' => $ticket->ticket_number]))
            ->assertOk()
            ->assertSee($ticket->title);
    }
}
