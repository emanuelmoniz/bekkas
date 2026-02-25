<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shipping_tiers', function (Blueprint $table) {
            $table->dropColumn(['is_default', 'use_for_default']);
        });
    }

    public function down(): void
    {
        Schema::table('shipping_tiers', function (Blueprint $table) {
            $table->boolean('is_default')->default(false)->after('active');
            $table->boolean('use_for_default')->default(false)->after('is_default');
        });
    }
};
