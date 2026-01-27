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
        if (!Schema::hasColumn('order_items', 'was_backordered')) {
            Schema::table('order_items', function (Blueprint $table) {
                $table->boolean('was_backordered')->default(false)->after('quantity');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('order_items', 'was_backordered')) {
            Schema::table('order_items', function (Blueprint $table) {
                $table->dropColumn('was_backordered');
            });
        }
    }
};
