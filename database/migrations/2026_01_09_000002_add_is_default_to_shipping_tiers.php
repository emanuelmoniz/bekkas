<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shipping_tiers', function (Blueprint $table) {
            $table->boolean('is_default')->default(false)->after('active');
        });

        // Set first active shipping tier as default if none exists
        $firstTier = DB::table('shipping_tiers')->where('active', true)->first();
        if ($firstTier) {
            DB::table('shipping_tiers')->where('id', $firstTier->id)->update(['is_default' => true]);
        }
    }

    public function down(): void
    {
        Schema::table('shipping_tiers', function (Blueprint $table) {
            $table->dropColumn('is_default');
        });
    }
};
