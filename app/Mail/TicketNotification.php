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

    /**
     * @var array|null Translation params when using a translation key
     */
    public ?array $eventParams = null;

    public function __construct(Ticket $ticket, TicketMessage $message, string $event, string $recipientName, ?array $eventParams = null)
    {
        $this->ticket = $ticket;
        $this->ticketMessage = $message;
        $this->eventLabel = $event; // key or literal; resolved in build()
        $this->recipientName = $recipientName;
        $this->eventParams = $eventParams;
    }

    public function build(): self
    {
        // Resolve event label at build time so the Mailable's locale is respected.
        $label = t($this->eventLabel, $this->eventParams ?? []) ?: $this->eventLabel;
        $this->eventLabel = $label;

        return $this
            ->from(config('mail.from.address'), config('mail.from.name', config('app.name', 'BEKKAS')))
            ->subject(t('tickets.email.subject', ['ticket_number' => $this->ticket->ticket_number ?? $this->ticket->id, 'event' => $this->eventLabel]) ?: "Ticket #{$this->ticket->ticket_number} – {$this->eventLabel}")
            ->markdown('emails.ticket-notification');
    }
}
