<?php

namespace App\Models;

use App\Mail\TicketNotification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class Ticket extends Model
{
    protected $fillable = [
        'user_id',
        'created_by',
        'ticket_category_id',
        'ticket_number',
        'title',
        'status',
        'opened_at',
        'due_date',
        'read_state',
        'last_message_at',
    ];

    protected $casts = [
        'read_state' => 'array',
        'opened_at' => 'datetime',
        'closed_at' => 'datetime',
        'last_message_at' => 'datetime',
        'due_date' => 'date',
        'uuid' => 'string',
        'ticket_number' => 'string',
    ];

    /**
     * Use UUID for route model binding (public URLs)
     */
    public function getRouteKeyName()
    {
        return 'uuid';
    }

    protected static function booted()
    {
        static::creating(function ($ticket) {
            $ticket->uuid = (string) Str::uuid();
            $ticket->opened_at = now();

            // Generate friendly ticket number like: TCK-A3F9-2B7E
            if (empty($ticket->ticket_number)) {
                do {
                    $ticketNumber = 'TCK-'.strtoupper(Str::random(4)).'-'.strtoupper(Str::random(4));
                } while (self::where('ticket_number', $ticketNumber)->exists());

                $ticket->ticket_number = $ticketNumber;
            }
        });
    }

    /* ================= RELATIONS ================= */

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function category()
    {
        return $this->belongsTo(
            TicketCategory::class,
            'ticket_category_id'
        );
    }

    public function messages()
    {
        return $this->hasMany(TicketMessage::class);
    }

    /* ================= READ / UNREAD ================= */

    public function markAsRead(int $userId): void
    {
        $state = $this->read_state ?? [];
        $state[$userId] = now();
        $this->update(['read_state' => $state]);
    }

    public function markAsUnread(int $userId): void
    {
        $state = $this->read_state ?? [];
        unset($state[$userId]);
        $this->update(['read_state' => $state]);
    }

    public function isUnreadFor(int $userId): bool
    {
        $lastRead = $this->read_state[$userId] ?? null;

        return ! $lastRead || $this->last_message_at > $lastRead;
    }

    /* ================= EMAIL NOTIFICATIONS  ================= */
    public function notifyParticipants(TicketMessage $message, string $eventLabel, int $actorId): void
    {
        $recipients = User::where(function ($q) {
            $q->where('id', $this->user_id)
                ->orWhereHas('roles', fn ($r) => $r->where('name', 'admin'));
        })
            ->where('id', '!=', $actorId)
            ->get();

        foreach ($recipients as $recipient) {
            // Admin recipients must always receive English emails; customers receive their configured language.
            $recipientLocale = $recipient->roles()->where('name', 'admin')->exists()
                ? 'en-UK'
                : ($recipient->language ?? app()->getLocale());

            Mail::to($recipient->email, $recipient->name)
                ->locale($recipientLocale)
                ->queue(new TicketNotification($this, $message, $eventLabel, $recipient->name));
        }
    }
}
