<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail as BaseVerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Carbon\Carbon;

class VerifyEmailNotification extends BaseVerifyEmail
{
    /**
     * Build the mail representation of the notification using DB-driven `t()` translations.
     */
    public function toMail($notifiable)
    {
        // Ensure DB helper uses the notifiable's preferred locale when resolving t()
        $previous = app()->getLocale();
        $locale = method_exists($notifiable, 'preferredLocale') && $notifiable->preferredLocale()
            ? $notifiable->preferredLocale()
            : $previous;

        app()->setLocale($locale);

        // Create the signed verification URL (same as framework implementation)
        $expiration = Config::get('auth.verification.expire', 60);

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes($expiration),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );

        $mail = (new MailMessage)
            ->subject(t('auth.verify_email_subject') ?: 'Verify Email Address')
            ->line(t('auth.verify_email_intro') ?: 'Please click the button below to verify your email address.')
            ->action(t('auth.verify_email_action') ?: 'Verify Email Address', $verificationUrl)
            ->line(t('auth.verify_email_outro') ?: 'If you did not create an account, no further action is required.');

        // restore previous locale to avoid side-effects
        app()->setLocale($previous);

        return $mail;
    }
}
