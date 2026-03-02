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
     * The helper ensures that when photos originate from products or projects
     * the primary image for each item is always returned before any non‑primary
     * photos.  When only one image per item is requested the primary image is
     * chosen if present; otherwise the newest photo is used.  For multiple
     * images the primary image is placed at the front and the rest are
     * sorted by descending creation date.  Finally the overall list is sorted
     * such that primaries precede non‑primaries and then by photo age, which
     * allows any global `max` limit to cut the newest entries.
     *
     * @return \Illuminate\Support\Collection
     */
    function image_scroller_images(array $config = [])
    {
        $max = isset($config['max']) && $config['max'] > 0 ? (int) $config['max'] : null;
        // we build an intermediate collection of "entries" containing path,
        // created_at and a priority flag.  this lets us reorder globally and
        // favour primary photos while still respecting the various limits.
        $entries = collect();

        /**
         * Prepare a list of photo models according to the requirements for a
         * single item (product or project).  The resulting array contains
         * entry arrays rather than raw paths.
         *
         * @param  \Illuminate\Database\Eloquent\Collection  $photos
         * @param  int|null  $limit
         */
        $addEntriesForPhotos = function ($photos, $limit) use (&$entries) {
            if ($photos->isEmpty()) {
                return;
            }

            // ensure we have a concrete collection so we can manipulate it
            $photos = $photos->sortByDesc('created_at')->values();

            // find primary photo if any
            $primary = $photos->firstWhere('is_primary', true);

            if ($limit === 1) {
                $chosen = $primary ?? $photos->first();
                if ($chosen) {
                    $entries->push([
                        'path' => $chosen->path,
                        'created_at' => $chosen->created_at,
                        'priority' => $chosen->is_primary ? 0 : 1,
                    ]);
                }

                return;
            }

            // when more than one image is requested we always output the
            // primary image first (if present) then the rest by descending
            // date.  the limit (if any) is applied *after* we have arranged the
            // set so that the primary image is retained.
            if ($primary) {
                $entries->push([
                    'path' => $primary->path,
                    'created_at' => $primary->created_at,
                    'priority' => 0,
                ]);
                $photos = $photos->reject(fn ($p) => $p->id === $primary->id)->values();
            }

            foreach ($photos as $photo) {
                $entries->push([
                    'path' => $photo->path,
                    'created_at' => $photo->created_at,
                    'priority' => 1,
                ]);
                if ($limit && $entries->count() >= $limit) {
                    break;
                }
            }
        };

        $collectFromProduct = function ($prod, $limit) use (&$addEntriesForPhotos) {
            if (! $prod) {
                return;
            }
            $photos = $prod->photos()->get();
            $addEntriesForPhotos($photos, $limit);
        };

        $collectFromProducts = function (array $conf, $globalMax) use (&$entries, &$addEntriesForPhotos) {
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
                $photos = $item->photos()->get();
                $addEntriesForPhotos($photos, $perItem);

                if ($globalMax && $entries->count() >= $globalMax) {
                    break;
                }
            }
        };

        $collectFromSingleProject = function ($proj, $limit) use (&$addEntriesForPhotos) {
            if (! $proj) {
                return;
            }
            $photos = $proj->photos()->get();
            $addEntriesForPhotos($photos, $limit);
        };

        $collectFromProjects = function (array $conf, $globalMax) use (&$entries, &$addEntriesForPhotos) {
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
                $photos = $item->photos()->get();
                $addEntriesForPhotos($photos, $perItem);

                if ($globalMax && $entries->count() >= $globalMax) {
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

        // once all requested sources have pushed entries we perform a
        // global sort; primary photos (priority 0) always come before others,
        // and within the same priority we sort by descending creation date.  a
        // final max constraint is applied after sorting.
        $ordered = $entries->sortBy([
            ['priority', 'asc'],
            ['created_at', 'desc'],
        ])->values();

        if ($max) {
            $ordered = $ordered->take($max);
        }

        return $ordered->map(function ($entry) {
            return asset('storage/'.ltrim($entry['path'], '/'));
        })->values();
    }
}

// maintenance helper ------------------------------------------------------
if (! function_exists('cleanup_unused_images')) {
    /**
     * Find image files on the public disk that are no longer referenced by any
     * database record.  Supports products, projects and social avatars, along
     * with their full‑resolution originals.  When run with the optional
     * `$actuallyDelete` flag the helper will remove the files it returns.
     *
     * The helper is not executed automatically; run it manually when you need
     * to clean up stray files (for example from tinker or an artisan command).
     *
     * Example usage from a shell:
     *
     * ```bash
     * php artisan tinker
     * >>> cleanup_unused_images();        // just lists unreferenced files
     * >>> cleanup_unused_images(true);    // deletes them as well
     * ```
     *
     * The returned array contains the paths relative to the storage disk
     * (e.g. `products/xyz.jpg`).
     *
     * @return array<string>
     */
    function cleanup_unused_images(bool $actuallyDelete = false): array
    {
        $disk = \Illuminate\Support\Facades\Storage::disk('public');

        // gather referenced paths from the database
        $referenced = collect();

        // product photos
        $referenced = $referenced->merge(
            \App\Models\ProductPhoto::query()
                ->pluck('path')
                ->filter()
        );
        $referenced = $referenced->merge(
            \App\Models\ProductPhoto::query()
                ->pluck('original_path')
                ->filter()
        );

        // project photos
        $referenced = $referenced->merge(
            \App\Models\ProjectPhoto::query()
                ->pluck('path')
                ->filter()
        );
        $referenced = $referenced->merge(
            \App\Models\ProjectPhoto::query()
                ->pluck('original_path')
                ->filter()
        );

        // social avatars – only the ones stored locally under /storage
        $avatars = \App\Models\SocialAccount::query()
            ->whereNotNull('avatar')
            ->pluck('avatar')
            ->filter(function ($a) {
                return is_string($a) && str_starts_with($a, '/storage/');
            })
            ->map(function ($a) {
                return ltrim(str_replace('/storage/', '', $a), '/');
            });

        $referenced = $referenced->merge($avatars);

        // normalise to remove leading slashes and dedupe
        $referenced = $referenced->map(fn ($p) => ltrim((string) $p, '/'))
            ->unique()
            ->values();

        // ensure we also protect originals for any thumbnail we know about
        // (some rows still have null `original_path` but the file is present).
        $extra = $referenced->map(function ($p) {
            // match e.g. "products/abc.jpg" or "projects/xyz.png" and
            // build corresponding "<folder>/originals/<file>" path.
            if (preg_match('#^(products|projects)/([^/]+)$#', $p, $m)) {
                return $m[1].'/originals/'.$m[2];
            }

            return null;
        })->filter();

        if ($extra->isNotEmpty()) {
            $referenced = $referenced->merge($extra)->unique()->values();
        }

        $unused = [];

        // directories we consider part of the image system
        $folders = ['products', 'projects', 'avatars'];

        foreach ($folders as $folder) {
            if (! $disk->exists($folder)) {
                continue;
            }
            $files = $disk->allFiles($folder);
            foreach ($files as $file) {
                if (! $referenced->contains($file)) {
                    $unused[] = $file;
                }
            }
        }

        if ($actuallyDelete && ! empty($unused)) {
            $disk->delete($unused);
        }

        return $unused;
    }
}
