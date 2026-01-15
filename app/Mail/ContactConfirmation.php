<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function build(): self
    {
        return $this
            ->from(config('mail.from.address'), config('mail.from.name', config('app.name', 'BEKKAS')))
            ->subject(t('contact.email.user_subject') ?: 'We received your message')
            ->markdown('emails.contact-confirmation');
    }
}
