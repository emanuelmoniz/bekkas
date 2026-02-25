<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipping_tier_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipping_tier_id')->constrained()->cascadeOnDelete();
            $table->string('locale', 5);
            $table->string('name');

            $table->unique(['shipping_tier_id', 'locale']);
        });

        // Migrate existing name_pt and name_en data
        $tiers = DB::table('shipping_tiers')->get(['id', 'name_pt', 'name_en']);

        foreach ($tiers as $tier) {
            DB::table('shipping_tier_translations')->insert([
                ['shipping_tier_id' => $tier->id, 'locale' => 'pt-PT', 'name' => $tier->name_pt],
                ['shipping_tier_id' => $tier->id, 'locale' => 'en-UK', 'name' => $tier->name_en],
            ]);
        }

        Schema::table('shipping_tiers', function (Blueprint $table) {
            $table->dropColumn(['name_pt', 'name_en']);
        });
    }

    public function down(): void
    {
        Schema::table('shipping_tiers', function (Blueprint $table) {
            $table->string('name_pt')->after('id');
            $table->string('name_en')->after('name_pt');
        });

        // Restore data from translations
        $translations = DB::table('shipping_tier_translations')
            ->whereIn('locale', ['pt-PT', 'en-UK'])
            ->get();

        $byTier = [];
        foreach ($translations as $t) {
            $byTier[$t->shipping_tier_id][$t->locale] = $t->name;
        }

        foreach ($byTier as $tierId => $locales) {
            DB::table('shipping_tiers')->where('id', $tierId)->update([
                'name_pt' => $locales['pt-PT'] ?? '',
                'name_en' => $locales['en-UK'] ?? '',
            ]);
        }

        Schema::dropIfExists('shipping_tier_translations');
    }
};
