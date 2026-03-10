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

        $messages = [
            'reason.required' => t('tickets.reason_required') ?: 'Please provide a reason.',
        ];

        $request->validate([
            'reason' => 'required|string',
        ], $messages);

        try {
            $ticket->update([
                'status' => 'closed',
                'closed_at' => now(),
                'close_reason' => $request->reason,
            ]);

            TicketMessage::create([
                'ticket_id' => $ticket->id,
                'user_id' => $user->id,
                'message' => (t('tickets.closed_reason') ?: 'Ticket closed. Reason:') . ' ' . $request->reason,
                'is_system' => true,
            ]);

            $ticket->notifyParticipants(
                $ticket->messages()->latest()->first(),
                'tickets.email.event.closed',
                $user->id,
                ['reason' => $request->reason]
            );

            // ✅ Mark unread for other participant
            $this->updateReadStateForSystemMessage($ticket, $user->id);

            return redirect()->route('tickets.show', $ticket)
                ->with('success', t('tickets.closed_success') ?: 'Ticket closed successfully.');
        } catch (\Throwable $e) {
            \Log::error('Ticket close failed', [
                'ticket_id' => $ticket->id,
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', t('tickets.close_failed') ?: 'Failed to close ticket. Please try again.');
        }
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

        $messages = [
            'reason.required' => t('tickets.reason_required') ?: 'Please provide a reason.',
        ];

        $request->validate([
            'reason' => 'required|string',
        ], $messages);

        $ticket->update([
            'status' => 'open',
            'reopen_reason' => $request->reason,
            'closed_at' => null,
        ]);

        TicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'message' => (t('tickets.reopened_reason') ?: 'Ticket reopened. Reason:') . ' ' . $request->reason,
            'is_system' => true,
        ]);

        $ticket->notifyParticipants(
            $ticket->messages()->latest()->first(),
            'tickets.email.event.reopened',
            $user->id,
            ['reason' => $request->reason]
        );

        // ✅ Mark unread for other participant
        $this->updateReadStateForSystemMessage($ticket, $user->id);

        return redirect()->route('tickets.show', $ticket)
            ->with('success', t('tickets.reopened_success') ?: 'Ticket reopened successfully.');
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
