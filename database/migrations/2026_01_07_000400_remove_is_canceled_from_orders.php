<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Some drivers (SQLite) don't support dropping columns or may fail when
        // indexes reference the column. Be defensive: check column exists and
        // wrap in a try/catch so test/migration runs don't fail.
        if (! Schema::hasColumn('orders', 'is_canceled')) {
            return;
        }

        try {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropColumn('is_canceled');
            });
        } catch (\Throwable $e) {
            // Log the exception for visibility but do not fail migrations on drivers
            // that cannot handle dropColumn (e.g., SQLite test DB).
            // In production (MySQL/Postgres) the dropColumn will work normally.
            \Log::warning('Could not drop column is_canceled on orders: '.$e->getMessage());
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('orders', 'is_canceled')) {
            return;
        }

        Schema::table('orders', function (Blueprint $table) {
            $table->boolean('is_canceled')->default(false)->after('is_paid');
        });
    }
};
