<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('orders')) {
            return;
        }

        if (! Schema::hasColumn('orders', 'uuid')) {
            Schema::table('orders', function (Blueprint $table) {
                // Nullable during migration so we can backfill existing rows
                $table->string('uuid', 36)->nullable()->unique()->after('id');
            });

            // Backfill existing orders with UUIDs
            $orders = DB::table('orders')->select('id')->get();
            foreach ($orders as $order) {
                DB::table('orders')->where('id', $order->id)->update([
                    'uuid' => (string) Str::uuid(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('orders')) {
            return;
        }

        if (Schema::hasColumn('orders', 'uuid')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropUnique(['uuid']);
                $table->dropColumn('uuid');
            });
        }
    }
};
