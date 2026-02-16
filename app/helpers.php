<?php

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

if (! function_exists('t')) {
    function t(string $key, array $replacements = []): string
    {
        // If the translations table doesn't exist yet, fall back to Laravel's __()
        try {
            if (! Schema::hasTable('static_translations')) {
                return (string) __($key, $replacements);
            }

            $cacheKey = 'static_translations_all';

            $all = Cache::rememberForever($cacheKey, function () {
                return \App\Models\StaticTranslation::all()
                    ->groupBy('locale')
                    ->map(function ($group) {
                        return $group->pluck('value', 'key')->toArray();
                    })->toArray();
            });

            $locale = app()->getLocale();
            $fallback = config('app.fallback_locale');

            if (isset($all[$locale]) && array_key_exists($key, $all[$locale])) {
                return apply_t_replacements($all[$locale][$key], $replacements);
            }

            if (isset($all[$fallback]) && array_key_exists($key, $all[$fallback])) {
                return apply_t_replacements($all[$fallback][$key], $replacements);
            }

            return (string) __($key, $replacements);
        } catch (\Throwable $e) {
            return (string) __($key, $replacements);
        }
    }
}

if (! function_exists('apply_t_replacements')) {
    function apply_t_replacements(string $value, array $replacements = []): string
    {
        if (empty($replacements)) {
            return $value;
        }

        $lookup = [];
        foreach ($replacements as $key => $replacement) {
            $lookup[':'.ltrim($key, ':')] = $replacement;
        }

        return strtr($value, $lookup);
    }
}

if (! function_exists('send_mails_enabled')) {
    /**
     * Return whether outbound emails are allowed.
     *
     * Priority: DB `configurations.send_mails_enabled` (when table exists) ->
     * config('mail.enabled') -> env('APP_EMAILS_ENABLED', true)
     */
    function send_mails_enabled(): bool
    {
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('configurations')) {
                $cfg = \App\Models\Configuration::latest()->first();
                if ($cfg && $cfg->send_mails_enabled !== null) {
                    return (bool) $cfg->send_mails_enabled;
                }
            }
        } catch (\Throwable $e) {
            // ignore and fallback to config/env
        }

        return (bool) (config('mail.enabled', env('APP_EMAILS_ENABLED', true)));
    }
}
