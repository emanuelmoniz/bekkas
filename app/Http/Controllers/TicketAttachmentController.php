<?php

namespace App\Http\Controllers;

use App\Models\TicketAttachment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TicketAttachmentController extends Controller
{
    public function download(TicketAttachment $attachment)
    {
        $user = Auth::user();
        $ticket = $attachment->message->ticket;

        if (! $user->hasRole('admin') && $ticket->user_id !== $user->id) {
            abort(403);
        }

        if (! Storage::disk('private')->exists($attachment->path)) {
            abort(404);
        }

        return Storage::disk('private')->download(
            $attachment->path,
            $attachment->original_name
        );
    }
}
