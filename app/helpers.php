<?php

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

if (! function_exists('t')) {
    function t(string $key): string
    {
        // If the translations table doesn't exist yet, fall back to Laravel's __()
        try {
            if (! Schema::hasTable('static_translations')) {
                return (string) __($key);
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
                return $all[$locale][$key];
            }

            if (isset($all[$fallback]) && array_key_exists($key, $all[$fallback])) {
                return $all[$fallback][$key];
            }

            return (string) __($key);
        } catch (\Throwable $e) {
            return (string) __($key);
        }
    }
}
