<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('products')) {
            return;
        }

        if (! Schema::hasColumn('products', 'uuid')) {
            Schema::table('products', function (Blueprint $table) {
                // Make uuid nullable for the migration; we'll backfill existing rows below
                $table->string('uuid', 36)->nullable()->unique()->after('id');
            });

            // Backfill existing records with UUIDs
            $products = DB::table('products')->select('id')->get();
            foreach ($products as $product) {
                DB::table('products')->where('id', $product->id)->update([
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
        if (! Schema::hasTable('products')) {
            return;
        }

        if (Schema::hasColumn('products', 'uuid')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropUnique(['uuid']);
                $table->dropColumn('uuid');
            });
        }
    }
};
