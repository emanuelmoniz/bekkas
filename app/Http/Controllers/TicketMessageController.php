<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Models\TicketAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Rules\Recaptcha;

class TicketMessageController extends Controller
{
    public function store(Request $request, Ticket $ticket)
    {
        $user = Auth::user();

        if (! $user->hasRole('admin') && $ticket->user_id !== $user->id) {
            abort(403);
        }

        if ($ticket->status === 'closed') {
            abort(403);
        }

        $request->validate([
            'message' => 'required|string',
            'files.*' => 'nullable|file|max:20480',
            'g-recaptcha-response' => ['required', new Recaptcha],
        ], [
            'g-recaptcha-response.required' => t('tickets.recaptcha_required') ?: 'Please verify that you are not a robot.',
        ]);

        // Create message
        $message = TicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'message' => $request->message,
        ]);

	$ticket->notifyParticipants(
    		$message,
    		'New message',
            $user->id
	);

        // Attach files
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $path = $file->store(
                    'tickets/' . $ticket->uuid,
                    'private'
                );

                TicketAttachment::create([
                    'ticket_message_id' => $message->id,
                    'original_name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'mime' => $file->getMimeType(),
                    'size' => $file->getSize(),
                ]);
            }
        }

        /*
         |-------------------------------------------------------
         | CORRECT READ / UNREAD HANDLING
         |-------------------------------------------------------
         | - Sender → read
         | - All other participants → unread
         */

        $state = $ticket->read_state ?? [];

        // Participants: ticket owner + creator
        $participants = collect([
            $ticket->user_id,
            $ticket->created_by,
        ])->unique();

        foreach ($participants as $participantId) {
            if ((int) $participantId === (int) $user->id) {
                // Sender reads own message
                $state[$participantId] = now();
            } else {
                // Others become unread
                unset($state[$participantId]);
            }
        }

        $ticket->update([
            'last_message_at' => now(),
            'read_state' => $state,
        ]);

        return back();
    }
}
