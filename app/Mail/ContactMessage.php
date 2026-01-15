<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ContactMessage extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public string $name;
    public string $email;
    public string $body;

    /**
     * Create a new message instance.
     */
    public function __construct(string $name, string $email, string $body)
    {
        $this->name = $name;
        $this->email = $email;
        $this->body = $body;
    }

    /**
     * Build the message.
     */
    public function build(): self
    {
        return $this
            ->from(config('mail.from.address'), config('mail.from.name', config('app.name', 'BEKKAS')))
            ->replyTo($this->email, $this->name)
            ->subject(t('contact.email.admin_subject', ['name' => $this->name]) ?: ('New contact message from ' . $this->name))
            ->markdown('emails.contact');
    }
}
