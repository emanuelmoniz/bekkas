<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Models\TicketMessage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketAdminController extends Controller
{
    protected function ensureAdmin()
    {
        if (! Auth::user()->hasRole('admin')) {
            abort(403);
        }
    }

    protected function systemMessage(Ticket $ticket, string $text)
    {
        $message = TicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => Auth::id(),
            'message' => $text,
            'is_system' => true,
        ]);

	$ticket->notifyParticipants(
    		$message,
    		'Administrative update',
    		Auth::id()
	);

        // unread for other participant
        $state = $ticket->read_state ?? [];
        unset($state[$ticket->user_id]);
        $state[Auth::id()] = now();

        $ticket->update([
            'last_message_at' => now(),
            'read_state' => $state,
        ]);
    }

    public function edit(Ticket $ticket)
    {
        $this->ensureAdmin();

        $users = User::where('active', true)->get();
        $categories = TicketCategory::with('translations')->get();

        // admin can change owner ONLY if admin created the ticket
        $canChangeOwner = $ticket->creator?->hasRole('admin') ?? false;

        return view('admin.tickets.edit', compact(
            'ticket',
            'users',
            'categories',
            'canChangeOwner'
        ));
    }

    public function update(Request $request, Ticket $ticket)
    {
        $this->ensureAdmin();

        $request->validate([
            'ticket_category_id' => 'required|exists:ticket_categories,id',
            'due_date' => 'nullable|date',
            'user_id' => 'nullable|exists:users,id',
        ]);

        /* ================= CATEGORY ================= */

        if ($ticket->ticket_category_id !== (int) $request->ticket_category_id) {
            $oldCat = optional($ticket->category?->translation())->name ?? '—';
            $newCat = TicketCategory::find($request->ticket_category_id)
                ->translation()?->name ?? '—';

            $ticket->update([
                'ticket_category_id' => $request->ticket_category_id,
            ]);

            $this->systemMessage(
                $ticket,
                "Category changed from {$oldCat} to {$newCat}."
            );
        }

        /* ================= DUE DATE ================= */

        if ($ticket->due_date !== $request->due_date) {
            $old = $ticket->due_date ?? '—';
            $new = $request->due_date ?? '—';

            $ticket->update(['due_date' => $request->due_date]);

            $this->systemMessage(
                $ticket,
                "Due date changed from {$old} to {$new}."
            );
        }

        /* ================= OWNER (CONDITIONAL) ================= */

        $canChangeOwner = $ticket->creator?->hasRole('admin') ?? false;

        if (
            $canChangeOwner &&
            $request->user_id &&
            $ticket->user_id !== (int) $request->user_id
        ) {
            $oldUser = $ticket->owner?->name ?? '—';
            $newUser = User::find($request->user_id)->name;

            $ticket->update(['user_id' => $request->user_id]);

            $this->systemMessage(
                $ticket,
                "Ticket reassigned from {$oldUser} to {$newUser}."
            );
        }

        return redirect()->route('tickets.show', $ticket);
    }
}
