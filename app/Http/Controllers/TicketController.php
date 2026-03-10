<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketAttachment;
use App\Models\TicketCategory;
use App\Models\TicketMessage;
use App\Models\User;
use App\Rules\Recaptcha;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // Client index: always show only logged-in user's tickets
        $query = Ticket::with('category.translations', 'owner')
            ->where('user_id', $user->id);

        // Client filters only

        // Ticket ID (support searching by numeric id or friendly ticket_number)
        if ($request->filled('ticket_id')) {
            $val = trim($request->ticket_id);
            $query->where(function ($q) use ($val) {
                $q->where('id', 'like', "%{$val}%")
                    ->orWhere('ticket_number', 'like', "%{$val}%");
            });
        }

        // Ticket title
        if ($request->filled('title')) {
            $query->where('title', 'like', '%'.trim($request->title).'%');
        }

        // Category
        if ($request->filled('category_id')) {
            $query->where('ticket_category_id', $request->category_id);
        }

        $tickets = $query
            ->latest('last_message_at')
            ->get();

        $categories = TicketCategory::with('translations')
            ->where('active', true)
            ->get();

        return view('tickets.index', compact('tickets', 'categories'));
    }

    public function create(Request $request)
    {
        $categories = TicketCategory::with('translations')
            ->where('active', true)
            ->get();

        $preselectedCategory = null;
        if ($request->filled('category')) {
            $preselectedCategory = $categories->firstWhere('slug', $request->query('category'));
        }

        return view('tickets.create', compact('categories', 'preselectedCategory'));
    }

    public function store(Request $request)
    {
        $authUser = Auth::user();

        // Build validation rules; require reCAPTCHA only when configured
        $rules = [
            'title' => 'required|string|max:255',
            'ticket_category_id' => 'required|exists:ticket_categories,id',
            'message' => 'required|string',
            'due_date' => 'nullable|date',
            'files.*' => 'nullable|file|max:20480',
        ];

        $messages = [
            'title.required' => t('tickets.title_required') ?: 'Please enter a title.',
            'title.max' => t('tickets.title_max') ?: 'Title cannot exceed 255 characters.',
            'ticket_category_id.required' => t('tickets.category_required') ?: 'Please select a category.',
            'message.required' => t('tickets.message_required') ?: 'Please enter a message.',
            'due_date.date' => t('tickets.due_date_invalid') ?: 'Please enter a valid date.',
            'files.*.max' => t('tickets.file_max') ?: 'File cannot exceed 20 MB.',
        ];

        if (! empty(config('services.recaptcha.secret_key'))) {
            $rules['g-recaptcha-response'] = ['required', new Recaptcha];
            $messages['g-recaptcha-response.required'] = t('tickets.recaptcha_required') ?: 'Please verify that you are not a robot.';
        }

        $request->validate($rules, $messages);

        // Client always creates ticket for themselves
        $ticket = Ticket::create([
            'user_id' => $authUser->id,
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
            'tickets.email.event.created',
            $authUser->id
        );

        return redirect()->route('tickets.show', $ticket)
            ->with('success', t('tickets.created_success') ?: 'Ticket created successfully!');
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
