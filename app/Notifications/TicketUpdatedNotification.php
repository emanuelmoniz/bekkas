<?php

namespace App\Notifications;

use App\Models\Ticket;
use App\Models\TicketMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class TicketUpdatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected Ticket $ticket,
        protected TicketMessage $message,
        protected string $eventLabel
    ) {}

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject("Ticket #{$this->ticket->uuid} – {$this->eventLabel}")
            ->greeting("Hello {$notifiable->name},")
            ->line("There is an update on a ticket you are involved in.")
            ->line("Ticket: {$this->ticket->title}")
            ->line("Status: " . ucfirst($this->ticket->status))
            ->line("Update type: {$this->eventLabel}")
            ->when(
                ! $this->message->is_system,
                fn ($mail) => $mail->line("Message:")
                                  ->line($this->message->message)
            )
            ->action(
                'View Ticket',
                route('tickets.show', $this->ticket)
            )
            ->line('This email was sent automatically.');
    }
}
