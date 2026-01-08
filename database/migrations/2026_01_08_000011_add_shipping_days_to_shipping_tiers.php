<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('shipping_tiers', function (Blueprint $table) {
            $table->unsignedInteger('shipping_days')->default(1)->after('cost_gross');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shipping_tiers', function (Blueprint $table) {
            $table->dropColumn('shipping_days');
        });
    }
};
