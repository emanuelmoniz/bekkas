<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketAttachment;
use App\Models\TicketMessage;
use App\Rules\Recaptcha;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        $rules = [
            'message' => 'required|string',
            'files.*' => 'nullable|file|max:20480',
        ];

        $messages = [
            'message.required' => t('tickets.message_required') ?: 'Please enter a message.',
        ];

        // If reCAPTCHA is configured, require it for non-admin users only
        if (! empty(config('services.recaptcha.secret_key')) && ! ($user && $user->hasRole('admin'))) {
            $rules['g-recaptcha-response'] = ['required', new Recaptcha];
            $messages['g-recaptcha-response.required'] = t('tickets.recaptcha_required') ?: 'Please verify that you are not a robot.';
        }

        try {
            $request->validate($rules, $messages);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::info('Ticket message validation failed', [
                'ticket_id' => $ticket->id,
                'user_id' => $user->id,
                'errors' => $e->validator->errors()->all(),
                'input' => $request->only('message'),
                'path' => request()->path(),
            ]);

            throw $e;
        }

        // Create message (log and fail gracefully)
        try {
            $message = TicketMessage::create([
                'ticket_id' => $ticket->id,
                'user_id' => $user->id,
                'message' => $request->message,
            ]);
        } catch (\Throwable $e) {
            \Log::error('Ticket message creation failed', [
                'ticket_id' => $ticket->id,
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'input' => $request->only('message'),
            ]);

            return back()
                ->withInput()
                ->withErrors(['message' => t('tickets.message_failed') ?: 'Failed to save message. Please try again.'])
                ->with('error', t('tickets.message_failed') ?: 'Failed to save message. Please try again.');
        }

        // Attach files (safe — don't fail entire request if an upload/store fails)
        $attachmentFailed = false;
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                try {
                    $path = $file->store('tickets/'.$ticket->uuid, 'private');

                    TicketAttachment::create([
                        'ticket_message_id' => $message->id,
                        'original_name' => $file->getClientOriginalName(),
                        'path' => $path,
                        'mime' => $file->getMimeType(),
                        'size' => $file->getSize(),
                    ]);
                } catch (\Throwable $e) {
                    \Log::error('Ticket attachment save failed', [
                        'ticket_id' => $ticket->id,
                        'message_id' => $message->id,
                        'error' => $e->getMessage(),
                    ]);
                    $attachmentFailed = true;
                }
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

        // Notify participants (non-blocking; failures should not discard message)
        try {
            $ticket->notifyParticipants(
                $message,
                'tickets.email.event.new_message',
                $user->id
            );
        } catch (\Throwable $e) {
            \Log::error('Ticket notification failed', [
                'ticket_id' => $ticket->id,
                'message_id' => $message->id,
                'error' => $e->getMessage(),
            ]);
        }

        $response = back()->with('success', t('tickets.message_sent_success') ?: 'Message sent successfully.');
        if ($attachmentFailed) {
            $response = $response->with('error', t('tickets.attachment_failed') ?: 'Some files failed to attach.');
        }

        return $response;
    }
}
