<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tax_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tax_id')->constrained()->cascadeOnDelete();
            $table->string('locale', 10);
            $table->string('name');

            $table->unique(['tax_id', 'locale']);
        });

        // Migrate existing name data to all configured locales
        $locales = array_keys(config('app.locales', ['pt-PT' => 'Português', 'en-UK' => 'English']));
        $taxes = DB::table('taxes')->get(['id', 'name']);

        foreach ($taxes as $tax) {
            foreach ($locales as $locale) {
                DB::table('tax_translations')->insert([
                    'tax_id' => $tax->id,
                    'locale' => $locale,
                    'name' => $tax->name,
                ]);
            }
        }

        Schema::table('taxes', function (Blueprint $table) {
            $table->dropColumn('name');
        });
    }

    public function down(): void
    {
        Schema::table('taxes', function (Blueprint $table) {
            $table->string('name')->after('id');
        });

        // Restore name from the fallback locale (en-UK) or first available translation
        $fallback = config('app.fallback_locale', 'en-UK');
        $translations = DB::table('tax_translations')
            ->where('locale', $fallback)
            ->get();

        foreach ($translations as $t) {
            DB::table('taxes')->where('id', $t->tax_id)->update(['name' => $t->name]);
        }

        Schema::dropIfExists('tax_translations');
    }
};
