<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketAttachment;
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
        $canChangeOwner = optional($ticket->creator)->hasRole('admin') ?? false;

        return view('admin.tickets.edit', compact(
            'ticket',
            'users',
            'categories',
            'canChangeOwner'
        ));
    }

    public function index(Request $request)
    {
        $this->ensureAdmin();

        $query = Ticket::with('category.translations', 'owner');

        if ($request->filled('ticket_id')) {
            $query->where('id', 'like', '%'.trim($request->ticket_id).'%');
        }

        if ($request->filled('title')) {
            $query->where('title', 'like', '%'.trim($request->title).'%');
        }

        if ($request->filled('category_id')) {
            $query->where('ticket_category_id', $request->category_id);
        }

        if ($request->filled('user')) {
            $query->whereHas('owner', function ($q) use ($request) {
                $q->where('name', 'like', '%'.trim($request->user).'%');
            });
        }

        if ($request->filled('email')) {
            $query->whereHas('owner', function ($q) use ($request) {
                $q->where('email', 'like', '%'.trim($request->email).'%');
            });
        }

        $tickets = $query->latest('last_message_at')->get();

        $categories = TicketCategory::with('translations')->get();

        return view('admin.tickets.index', compact('tickets', 'categories'));
    }

    public function show(Ticket $ticket)
    {
        $this->ensureAdmin();

        $ticket->load([
            'messages.user',
            'messages.attachments',
            'category.translations',
            'owner',
            'creator',
        ]);

        // Mark ticket as read for admin
        if ($ticket->isUnreadFor(Auth::id())) {
            $ticket->markAsRead(Auth::id());
        }

        return view('admin.tickets.show', compact('ticket'));
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
            $oldCat = optional(optional($ticket->category)->translation())->name ?? '—';
            $newCat = optional(optional(TicketCategory::find($request->ticket_category_id))->translation())->name ?? '—';

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

        $canChangeOwner = optional($ticket->creator)->hasRole('admin') ?? false;

        if (
            $canChangeOwner &&
            $request->user_id &&
            $ticket->user_id !== (int) $request->user_id
        ) {
            $oldUser = optional($ticket->owner)->name ?? '—';
            $newUser = User::find($request->user_id)->name;

            $ticket->update(['user_id' => $request->user_id]);

            $this->systemMessage(
                $ticket,
                "Ticket reassigned from {$oldUser} to {$newUser}."
            );
        }

        return redirect()->route('admin.tickets.index');
    }

    public function create()
    {
        $this->ensureAdmin();

        $categories = TicketCategory::with('translations')->get();
        $users = User::where('active', true)->get();

        return view('admin.tickets.create', compact('categories', 'users'));
    }

    public function store(Request $request)
    {
        $this->ensureAdmin();

        $authUser = Auth::user();

        $request->validate([
            'title' => 'required|string|max:255',
            'ticket_category_id' => 'required|exists:ticket_categories,id',
            'message' => 'required|string',
            'due_date' => 'nullable|date',
            'user_id' => 'required|exists:users,id',
            'files.*' => 'nullable|file|max:20480',
        ]);

        // Admin creates ticket on behalf of specified user
        $ticket = Ticket::create([
            'user_id' => (int) $request->user_id,
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

        $message = TicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => $authUser->id,
            'message' => $request->message,
        ]);

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $path = $file->store(
                    'tickets/'.$ticket->uuid,
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

        $ticket->notifyParticipants(
            $message,
            'New ticket created',
            $authUser->id
        );

        return redirect()->route('admin.tickets.show', $ticket);
    }
}
