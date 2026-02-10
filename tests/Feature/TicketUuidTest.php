<?php

namespace Tests\Feature;

use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketUuidTest extends TestCase
{
    use RefreshDatabase;

    public function test_ticket_urls_use_uuid()
    {
        $owner = User::factory()->create();
        $category = TicketCategory::create(['active' => true]);

        $ticket = Ticket::create([
            'user_id' => $owner->id,
            'created_by' => $owner->id,
            'ticket_category_id' => $category->id,
            'title' => 'UUID test ticket',
            'status' => 'open',
        ]);

        $url = route('tickets.show', $ticket);

        $this->assertStringContainsString($ticket->uuid, $url);
        $this->assertStringNotContainsString('/'.$ticket->id, $url);
    }

    public function test_view_ticket_by_uuid_returns_ok()
    {
        $owner = User::factory()->create();
        $category = TicketCategory::create(['active' => true]);

        $ticket = Ticket::create([
            'user_id' => $owner->id,
            'created_by' => $owner->id,
            'ticket_category_id' => $category->id,
            'title' => 'UUID access test',
            'status' => 'open',
        ]);

        $this->actingAs($owner)
            ->get(route('tickets.show', $ticket))
            ->assertOk();
    }
}
