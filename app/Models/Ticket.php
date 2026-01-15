<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Mail\TicketNotification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use App\Models\User;
use App\Models\TicketMessage;

class Ticket extends Model
{
    protected $fillable = [
        'user_id',
        'created_by',
        'ticket_category_id',
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
    ];

    protected static function booted()
    {
        static::creating(function ($ticket) {
            $ticket->uuid = (string) Str::uuid();
            $ticket->opened_at = now();
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
            Mail::to($recipient->email, $recipient->name)
                ->queue(new TicketNotification($this, $message, $eventLabel, $recipient->name));
        }
    }
}
