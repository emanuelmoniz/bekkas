<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('products')) {
            return;
        }

        // Add column only if it does not already exist (guards against duplicate runs
        // and migration ordering issues in SQLite / test environments).
        if (! Schema::hasColumn('products', 'production_time')) {
            Schema::table('products', function (Blueprint $table) {
                if (Schema::getConnection()->getDriverName() === 'sqlite') {
                    $table->integer('production_time')->default(0);
                } else {
                    $table->integer('production_time')->default(0)->after('weight');
                }
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('products')) {
            return;
        }

        if (Schema::hasColumn('products', 'production_time')) {
            Schema::table('products', function (Blueprint $table) {
                // DROP COLUMN on SQLite may be unsupported in certain contexts — guard first.
                $table->dropColumn('production_time');
            });
        }
    }
};
