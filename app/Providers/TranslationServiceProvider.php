<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use App\Models\StaticTranslation;

class TranslationServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Nothing to bind
    }

    public function boot(): void
    {
        // Load translations into cache keyed by locale if the table exists
        if (Schema::hasTable('static_translations')) {
            $this->loadTranslationsToCache();
        }

        if (! function_exists('t')) {
            function t(string $key): string
            {
                // If the translations table doesn't exist (migrations not run yet), fall back to __()
                if (! Schema::hasTable('static_translations')) {
                    return (string) __($key);
                }

                $locale = app()->getLocale();
                $fallback = config('app.fallback_locale');
                $cacheKey = 'static_translations_all';

                // Protect against any DB/cache errors — fall back to __()
                try {
                    $all = Cache::rememberForever($cacheKey, function () {
                        return StaticTranslation::all()->groupBy('locale')->map(function ($group) {
                            return $group->pluck('value', 'key')->toArray();
                        })->toArray();
                    });

                    // Try current locale
                    if (isset($all[$locale]) && array_key_exists($key, $all[$locale])) {
                        return $all[$locale][$key];
                    }

                    // Fallback locale
                    if (isset($all[$fallback]) && array_key_exists($key, $all[$fallback])) {
                        return $all[$fallback][$key];
                    }
                } catch (\Throwable $e) {
                    return (string) __($key);
                }

                // Last resort: use Laravel's __()
                return (string) __($key);
            }
        }
    }

    protected function loadTranslationsToCache(): void
    {
        $cacheKey = 'static_translations_all';

        if (! Cache::has($cacheKey)) {
            Cache::forever($cacheKey, StaticTranslation::all()->groupBy('locale')->map(function ($group) {
                return $group->pluck('value', 'key')->toArray();
            })->toArray());
        }
    }
}
