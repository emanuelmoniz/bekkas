<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('region_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('region_id')->constrained()->cascadeOnDelete();
            $table->string('locale', 10);
            $table->string('name');

            $table->unique(['region_id', 'locale']);
        });

        // Migrate existing name data to all configured locales
        $locales = array_keys(config('app.locales', ['pt-PT' => 'Português', 'en-UK' => 'English']));
        $regions = DB::table('regions')->get(['id', 'name']);

        foreach ($regions as $region) {
            foreach ($locales as $locale) {
                DB::table('region_translations')->insert([
                    'region_id' => $region->id,
                    'locale'    => $locale,
                    'name'      => $region->name,
                ]);
            }
        }

        Schema::table('regions', function (Blueprint $table) {
            $table->dropColumn('name');
        });
    }

    public function down(): void
    {
        Schema::table('regions', function (Blueprint $table) {
            $table->string('name')->after('country_id');
        });

        // Restore name from the fallback locale (en-UK) or first available translation
        $fallback = config('app.fallback_locale', 'en-UK');
        $translations = DB::table('region_translations')
            ->where('locale', $fallback)
            ->get();

        foreach ($translations as $t) {
            DB::table('regions')->where('id', $t->region_id)->update(['name' => $t->name]);
        }

        Schema::dropIfExists('region_translations');
    }
};
