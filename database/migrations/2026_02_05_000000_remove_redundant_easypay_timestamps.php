<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Backfill canonical timestamps then drop redundant columns
        // - created_at <- COALESCE(created_at, timestamp)
        // - updated_at <- COALESCE(last_update_timestamp, updated_at)
        DB::table('easypay_checkout_sessions')
            ->whereNotNull('timestamp')
            ->update(['created_at' => DB::raw('COALESCE(created_at, `timestamp`)')]);

        DB::table('easypay_checkout_sessions')
            ->whereNotNull('last_update_timestamp')
            ->update(['updated_at' => DB::raw('COALESCE(updated_at, `last_update_timestamp`)')]);

        Schema::table('easypay_checkout_sessions', function (Blueprint $table) {
            if (Schema::hasColumn('easypay_checkout_sessions', 'timestamp')) {
                $table->dropColumn('timestamp');
            }
            if (Schema::hasColumn('easypay_checkout_sessions', 'last_update_timestamp')) {
                $table->dropColumn('last_update_timestamp');
            }
        });
    }

    public function down(): void
    {
        Schema::table('easypay_checkout_sessions', function (Blueprint $table) {
            if (! Schema::hasColumn('easypay_checkout_sessions', 'timestamp')) {
                $table->timestamp('timestamp')->useCurrent()->after('message');
            }
            if (! Schema::hasColumn('easypay_checkout_sessions', 'last_update_timestamp')) {
                $table->timestamp('last_update_timestamp')->nullable()->useCurrentOnUpdate()->after('timestamp');
            }
        });

        // Restore values from created_at/updated_at
        DB::table('easypay_checkout_sessions')
            ->update(['timestamp' => DB::raw('COALESCE(`timestamp`, created_at)')]);

        DB::table('easypay_checkout_sessions')
            ->update(['last_update_timestamp' => DB::raw('COALESCE(`last_update_timestamp`, updated_at)')]);
    }
};
