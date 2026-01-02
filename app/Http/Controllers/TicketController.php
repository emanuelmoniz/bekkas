<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Models\TicketMessage;
use App\Models\TicketAttachment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $tickets = $user->hasRole('admin')
            ? Ticket::with('category.translations', 'owner')
                ->latest('last_message_at')
                ->get()
            : Ticket::with('category.translations', 'owner')
                ->where('user_id', $user->id)
                ->latest('last_message_at')
                ->get();

        return view('tickets.index', compact('tickets'));
    }

    public function create()
    {
        $user = Auth::user();

        $categories = TicketCategory::with('translations')
            ->where('active', true)
            ->get();

        $users = $user->hasRole('admin')
            ? User::where('active', true)->get()
            : collect();

        return view('tickets.create', compact('categories', 'users'));
    }

    public function store(Request $request)
    {
        $authUser = Auth::user();

        $request->validate([
            'title' => 'required|string|max:255',
            'ticket_category_id' => 'required|exists:ticket_categories,id',
            'message' => 'required|string',
            'due_date' => 'nullable|date',
            'user_id' => 'nullable|exists:users,id',
            'files.*' => 'nullable|file|max:20480',
        ]);

        // Determine ticket owner
        $ownerId = $authUser->hasRole('admin') && $request->user_id
            ? (int) $request->user_id
            : $authUser->id;

        // Create ticket
        $ticket = Ticket::create([
            'user_id' => $ownerId,
            'created_by' => $authUser->id,
            'ticket_category_id' => $request->ticket_category_id,
            'title' => $request->title,
            'status' => 'open',
            'opened_at' => now(),
            'due_date' => $request->due_date,
            'last_message_at' => now(),
            'read_state' => [
                $authUser->id => now(),
            ],
        ]);

        // Create initial message
        $message = TicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => $authUser->id,
            'message' => $request->message,
        ]);

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

	// Send notification
	$ticket->notifyParticipants(
    		$message,
    		'New ticket created',
    		$authUser->id
	);

        return redirect()->route('tickets.show', $ticket);
    }

    public function show(Ticket $ticket)
    {
        $user = Auth::user();

        if (! $user->hasRole('admin') && $ticket->user_id !== $user->id) {
            abort(403);
        }

        if ($ticket->isUnreadFor($user->id)) {
            $ticket->markAsRead($user->id);
        }

        $ticket->load([
            'messages.user',
            'messages.attachments',
            'category.translations',
        ]);

        return view('tickets.show', compact('ticket'));
    }
}
