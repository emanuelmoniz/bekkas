<?php

namespace App\Providers;

use App\Models\Configuration;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

class ConfigurationServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Defensive: avoid running while migrations/tables aren't available.
        try {
            if (! Schema::hasTable('configurations')) {
                return;
            }
        } catch (\Exception $e) {
            return;
        }

        // Use the highest `id` row to avoid `created_at` ties when multiple
        // configuration rows are created within the same second in tests/seeders.
        $cfg = Configuration::orderBy('id', 'desc')->first();
        if (! $cfg) {
            return;
        }

        $map = [
            // DB field => config key(s)
            'app_name' => ['app.name'],

            // Mail
            'mail_admin' => ['mail.admin_address'],
            'mail_contact' => ['mail.contact_address'],
            'smtp_server_host' => ['mail.mailers.smtp.host'],
            'smtp_server_port' => ['mail.mailers.smtp.port'],
            'smtp_username' => ['mail.mailers.smtp.username'],
            'smtp_password' => ['mail.mailers.smtp.password'],
            'smtp_encryptation' => ['mail.mailers.smtp.encryption'],
            'smtp_mail_from' => ['mail.from.address'],

            // Recaptcha
            'google_recaptcha_site_key' => ['services.recaptcha.site_key'],
            'google_recaptcha_secret_key' => ['services.recaptcha.secret_key'],

            // Google social login
            'google_socialite_enabled' => ['services.google.enabled'],
            'google_client_id' => ['services.google.client_id'],
            'google_client_secret' => ['services.google.client_secret'],
            'google_redirect' => ['services.google.redirect'],

            // Microsoft social login
            'microsoft_socialite_enabled' => ['services.microsoft.enabled'],
            'microsoft_client_id' => ['services.microsoft.client_id'],
            'microsoft_client_secret' => ['services.microsoft.client_secret'],
            'microsoft_redirect' => ['services.microsoft.redirect'],
            'microsoft_tenant' => ['services.microsoft.tenant'],

            // Easypay
            'easypay_enabled' => ['easypay.enabled'],
            'easypay_api_key' => ['easypay.api_key'],
            'easypay_id' => ['easypay.id'],
            'easypay_webhook_secret' => ['easypay.webhook_secret'],
            'easypay_webhook_header' => ['easypay.webhook_header'],
            'easypay_webhook_user' => ['easypay.webhook_user'],
            'easypay_webhook_pass' => ['easypay.webhook_pass'],
            'easypay_url_url' => ['easypay.base_url'],
            'easypay_sdk_url' => ['easypay.sdk_url'],
            'easypay_payment_methods' => ['easypay.payment_methods'],
            'easypay_session_ttl' => ['easypay.session_ttl'],
            'easypay_mb_ttl' => ['easypay.mb_ttl'],

            // Mail switch (DB override for APP_EMAILS_ENABLED)
            'send_mails_enabled' => ['mail.enabled'],

            // Store feature toggle (DB should take precedence over APP_STORE_ENABLED)
            'store_enabled' => ['app.store_enabled'],

            // Tax feature toggle (DB override for APP_TAX_ENABLED)
            'tax_enabled' => ['app.tax_enabled'],
        ];

        foreach ($map as $field => $keys) {
            if (! array_key_exists($field, $cfg->getAttributes())) {
                continue;
            }

            $value = $cfg->{$field};
            if ($value === null || $value === '') {
                continue; // fallback to env/config default
            }

            foreach ($keys as $key) {
                // cast numeric strings to int where appropriate
                if (is_string($value) && is_numeric($value) && str_starts_with($key, 'easypay.') && (str_contains($key, 'ttl') || str_ends_with($key, '.port'))) {
                    config()->set($key, (int) $value);
                    continue;
                }

                config()->set($key, $value);
            }
        }

        // Mailer-level fallback: when DB disables outbound mail, route the
        // app's default mailer to the `disabled` mailer (which uses `array`
        // transport and discards deliveries). This centralises the behaviour
        // so individual call-sites don't need guards.
        if (! config('mail.enabled', env('APP_EMAILS_ENABLED', true))) {
            config()->set('mail.default', 'disabled');
        } else {
            // ensure default remains the configured env/default value
            config()->set('mail.default', env('MAIL_MAILER', config('mail.default')));
        }
    }

    /**
     * Register services.
     */
    public function register(): void
    {
        // no-op
    }
}
