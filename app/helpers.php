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

// image scroller helper -----------------------------------------------------
if (! function_exists('image_scroller_images')) {
    /**
     * Build a collection of image URLs according to the configuration options
     * originally used by the class‑based ImageScroller component.
     *
     * @param  array  $config
     * @return \Illuminate\Support\Collection
     */
    function image_scroller_images(array $config = [])
    {
        $max = isset($config['max']) && $config['max'] > 0 ? (int) $config['max'] : null;
        $images = collect();

        $collectFromProduct = function ($prod, $limit) use (&$images) {
            if (! $prod) {
                return;
            }
            $photos = $prod->photos()->orderByDesc('created_at');
            if ($limit) {
                $photos->take($limit);
            }
            $images->push(...$photos->pluck('path')->all());
        };

        $collectFromProducts = function (array $conf, $globalMax) use (&$images) {
            $query = App\Models\Product::query();

            if (! empty($conf['ids'])) {
                $query->whereIn('id', (array) $conf['ids']);
            }
            if (array_key_exists('featured', $conf) && ! is_null($conf['featured'])) {
                $query->where('is_featured', $conf['featured']);
            }
            if (array_key_exists('active', $conf) && ! is_null($conf['active'])) {
                $query->where('active', $conf['active']);
            }

            $perItem = isset($conf['per_item']) && $conf['per_item'] > 0 ? (int) $conf['per_item'] : null;

            $items = $query->orderByDesc('created_at')->get();

            foreach ($items as $item) {
                $photos = $item->photos()->orderByDesc('created_at');
                if ($perItem) {
                    $photos->take($perItem);
                }
                $images->push(...$photos->pluck('path')->all());

                if ($globalMax && $images->count() >= $globalMax) {
                    break;
                }
            }
        };

        $collectFromSingleProject = function ($proj, $limit) use (&$images) {
            if (! $proj) {
                return;
            }
            $photos = $proj->photos()->orderByDesc('created_at');
            if ($limit) {
                $photos->take($limit);
            }
            $images->push(...$photos->pluck('path')->all());
        };

        $collectFromProjects = function (array $conf, $globalMax) use (&$images) {
            $query = App\Models\Project::query();

            if (! empty($conf['ids'])) {
                $query->whereIn('id', (array) $conf['ids']);
            }
            if (array_key_exists('featured', $conf) && ! is_null($conf['featured'])) {
                $query->where('is_featured', $conf['featured']);
            }
            if (array_key_exists('active', $conf) && ! is_null($conf['active'])) {
                $query->where('is_active', $conf['active']);
            }

            $perItem = isset($conf['per_item']) && $conf['per_item'] > 0 ? (int) $conf['per_item'] : null;

            $items = $query->orderByDesc('created_at')->get();

            foreach ($items as $item) {
                $photos = $item->photos()->orderByDesc('created_at');
                if ($perItem) {
                    $photos->take($perItem);
                }
                $images->push(...$photos->pluck('path')->all());

                if ($globalMax && $images->count() >= $globalMax) {
                    break;
                }
            }
        };

        if (isset($config['product'])) {
            $collectFromProduct(App\Models\Product::find($config['product']), $max);
        }

        if (isset($config['products']) && is_array($config['products'])) {
            $collectFromProducts($config['products'], $max);
        }

        if (isset($config['project'])) {
            $collectFromSingleProject(App\Models\Project::find($config['project']), $max);
        }

        if (isset($config['projects']) && is_array($config['projects'])) {
            $collectFromProjects($config['projects'], $max);
        }

        if ($max) {
            $images = $images->take($max);
        }

        return $images->map(function ($path) {
            return asset('storage/' . ltrim($path, '/'));
        })->values();
    }
}
