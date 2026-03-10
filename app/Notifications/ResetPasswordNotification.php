<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as BaseResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends BaseResetPassword
{
    /**
     * Build a DB-localized reset-password email to keep all lines in one locale.
     */
    protected function buildMailMessage($url)
    {
        $minutes = (int) config('auth.passwords.'.config('auth.defaults.passwords').'.expire');

        return (new MailMessage)
            ->subject(t('auth.reset_password_email_subject') ?: 'Reset Password Notification')
            ->line(t('auth.reset_password_email_intro') ?: 'You are receiving this email because we received a password reset request for your account.')
            ->action(t('auth.reset_password_email_action') ?: 'Reset Password', $url)
            ->line(t('auth.reset_password_email_expire', ['count' => $minutes]) ?: "This password reset link will expire in {$minutes} minutes.")
            ->line(t('auth.reset_password_email_outro') ?: 'If you did not request a password reset, no further action is required.');
    }
}
