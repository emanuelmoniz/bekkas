<?php

namespace Database\Seeders;

use App\Models\Locale;
use Illuminate\Database\Seeder;

class LocaleSeeder extends Seeder
{
    /**
     * Reads config('app.locales') and upserts a row in the locales table for
     * each entry.  Marks the application default locale (config('app.locale'))
     * as is_default = true; clears is_default on all others.
     *
     * Safe to run multiple times.
     */
    public function run(): void
    {
        foreach (config('app.locales', []) as $code => $name) {
            Locale::updateOrCreate(
                ['code' => $code],
                ['name' => $name, 'is_active' => true]
            );
        }

        $default = config('app.locale');
        if ($default) {
            Locale::where('code', '!=', $default)->update(['is_default' => false]);
            Locale::where('code', $default)->update(['is_default' => true]);
        }
    }
}
