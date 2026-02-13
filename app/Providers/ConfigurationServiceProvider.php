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

        $cfg = Configuration::latest()->first();
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
    }

    /**
     * Register services.
     */
    public function register(): void
    {
        // no-op
    }
}
