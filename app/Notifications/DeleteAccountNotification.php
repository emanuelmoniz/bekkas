<?php

namespace App\Notifications;

use Carbon\Carbon;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;

class DeleteAccountNotification extends Notification
{
    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Build the mail message containing a signed, time-limited deletion URL.
     */
    public function toMail($notifiable)
    {
        $previous = app()->getLocale();
        $locale = method_exists($notifiable, 'preferredLocale') && $notifiable->preferredLocale()
            ? $notifiable->preferredLocale()
            : $previous;

        app()->setLocale($locale);

        $expiration = Config::get('auth.verification.expire', 60);

        $deleteUrl = URL::temporarySignedRoute(
            'profile.delete.confirm',
            Carbon::now()->addMinutes($expiration),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );

        $mail = (new MailMessage)
            ->subject(t('profile.delete_by_email_subject') ?: 'Confirm account deletion')
            ->line(t('profile.delete_by_email_intro') ?: 'Click the button below to confirm deletion of your account.')
            ->action(t('profile.delete_by_email_action') ?: 'Delete my account', $deleteUrl)
            ->line(t('profile.delete_by_email_outro') ?: 'If you did not request this, ignore this email.');

        Log::info('Built account-deletion email', ['email' => $notifiable->getEmailForVerification(), 'locale' => $locale]);

        app()->setLocale($previous);

        return $mail;
    }
}
