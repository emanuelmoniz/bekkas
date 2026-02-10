<?php

namespace App\Mail;

use App\Models\Ticket;
use App\Models\TicketMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TicketNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public Ticket $ticket;

    public TicketMessage $ticketMessage;

    public string $eventLabel;

    public string $recipientName;

    public function __construct(Ticket $ticket, TicketMessage $message, string $eventLabel, string $recipientName)
    {
        $this->ticket = $ticket;
        $this->ticketMessage = $message;
        $this->eventLabel = $eventLabel;
        $this->recipientName = $recipientName;
    }

    public function build(): self
    {
        return $this
            ->from(config('mail.from.address'), config('mail.from.name', config('app.name', 'BEKKAS')))
            ->subject(t('tickets.email.subject', ['uuid' => $this->ticket->uuid, 'event' => $this->eventLabel]) ?: "Ticket #{$this->ticket->uuid} – {$this->eventLabel}")
            ->markdown('emails.ticket-notification');
    }
}
