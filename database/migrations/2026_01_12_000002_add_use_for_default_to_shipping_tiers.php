<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shipping_tiers', function (Blueprint $table) {
            $table->boolean('use_for_default')->default(false)->after('is_default');
        });
    }

    public function down(): void
    {
        Schema::table('shipping_tiers', function (Blueprint $table) {
            $table->dropColumn('use_for_default');
        });
    }
};
