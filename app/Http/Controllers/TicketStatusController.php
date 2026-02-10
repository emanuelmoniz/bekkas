<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class TicketStatusController extends Controller
{
    protected function updateReadStateForSystemMessage(Ticket $ticket, int $actorId)
    {
        $state = $ticket->read_state ?? [];

        $participants = collect([
            $ticket->user_id,
            $ticket->created_by,
        ])->unique();

        foreach ($participants as $participantId) {
            if ((int) $participantId === (int) $actorId) {
                $state[$participantId] = now();
            } else {
                unset($state[$participantId]);
            }
        }

        $ticket->update([
            'last_message_at' => now(),
            'read_state' => $state,
        ]);
    }

    public function close(Request $request, Ticket $ticket)
    {
        $user = Auth::user();

        if (! $user->hasRole('admin') && $ticket->user_id !== $user->id) {
            abort(403);
        }

        if ($ticket->status === 'closed') {
            return back();
        }

        $request->validate([
            'reason' => 'required|string',
        ]);

        $ticket->update([
            'status' => 'closed',
            'closed_at' => now(),
            'close_reason' => $request->reason,
        ]);

        TicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'message' => 'Ticket closed. Reason: '.$request->reason,
            'is_system' => true,
        ]);

        $ticket->notifyParticipants(
            $ticket->messages()->latest()->first(),
            'Ticket closed',
            $user->id
        );

        // ✅ Mark unread for other participant
        $this->updateReadStateForSystemMessage($ticket, $user->id);

        return redirect()->route('tickets.show', $ticket);
    }

    public function reopen(Request $request, Ticket $ticket)
    {
        $user = Auth::user();

        if (! $user->hasRole('admin') && $ticket->user_id !== $user->id) {
            abort(403);
        }

        if ($ticket->status === 'open') {
            return back();
        }

        $request->validate([
            'reason' => 'required|string',
        ]);

        $ticket->update([
            'status' => 'open',
            'reopen_reason' => $request->reason,
            'closed_at' => null,
        ]);

        TicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'message' => 'Ticket reopened. Reason: '.$request->reason,
            'is_system' => true,
        ]);

        $ticket->notifyParticipants(
            $ticket->messages()->latest()->first(),
            'Ticket reopened',
            $user->id
        );

        // ✅ Mark unread for other participant
        $this->updateReadStateForSystemMessage($ticket, $user->id);

        return redirect()->route('tickets.show', $ticket);
    }

    public function markUnread(Request $request, Ticket $ticket)
    {
        $user = Auth::user();

        if (! $user->hasRole('admin') && $ticket->user_id !== $user->id) {
            abort(403);
        }

        // Mark ticket as unread for current user
        $ticket->markAsUnread($user->id);

        // Redirect based on which route was called
        $currentRoute = Route::currentRouteName();

        return $currentRoute === 'admin.tickets.mark-unread'
            ? redirect()->route('admin.tickets.index')
            : redirect()->route('tickets.index');
    }
}
