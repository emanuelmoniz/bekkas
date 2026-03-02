<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('country_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')->constrained()->cascadeOnDelete();
            $table->string('locale', 10);
            $table->string('name');

            $table->unique(['country_id', 'locale']);
        });

        // Migrate existing name_pt / name_en columns into translations
        $locales = array_keys(config('app.locales', ['pt-PT' => 'Português', 'en-UK' => 'English']));
        $countries = DB::table('countries')->get(['id', 'name_pt', 'name_en']);

        foreach ($countries as $country) {
            foreach ($locales as $locale) {
                // Map locale to the column that best matches
                $name = match (true) {
                    str_starts_with($locale, 'pt') => $country->name_pt,
                    str_starts_with($locale, 'en') => $country->name_en,
                    default => $country->name_en,
                };

                DB::table('country_translations')->insert([
                    'country_id' => $country->id,
                    'locale' => $locale,
                    'name' => $name,
                ]);
            }
        }

        Schema::table('countries', function (Blueprint $table) {
            $table->dropIndex(['is_active', 'name_pt']);
            $table->dropColumn(['name_pt', 'name_en']);
        });
    }

    public function down(): void
    {
        Schema::table('countries', function (Blueprint $table) {
            $table->string('name_pt')->after('id');
            $table->string('name_en')->after('name_pt');
        });

        // Restore names from translations
        $fallback = config('app.fallback_locale', 'en-UK');
        $translations = DB::table('country_translations')
            ->where('locale', $fallback)
            ->get(['country_id', 'name']);

        foreach ($translations as $t) {
            DB::table('countries')
                ->where('id', $t->country_id)
                ->update(['name_en' => $t->name]);
        }

        $ptTranslations = DB::table('country_translations')
            ->where('locale', 'pt-PT')
            ->get(['country_id', 'name']);

        foreach ($ptTranslations as $t) {
            DB::table('countries')
                ->where('id', $t->country_id)
                ->update(['name_pt' => $t->name]);
        }

        Schema::table('countries', function (Blueprint $table) {
            $table->index(['is_active', 'name_pt']);
        });

        Schema::dropIfExists('country_translations');
    }
};
