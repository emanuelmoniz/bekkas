<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Guard: if the base table doesn't exist (migration ordering) skip safely
        if (! Schema::hasTable('shipping_tiers')) {
            return;
        }

        // For SQLite (used in tests) avoid unsupported DROP COLUMN operations —
        // add a nullable tax_id placeholder instead and skip dropping tax_percentage.
        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            Schema::table('shipping_tiers', function (Blueprint $table) {
                if (! Schema::hasColumn('shipping_tiers', 'tax_id')) {
                    $table->unsignedBigInteger('tax_id')->nullable()->after('cost_gross');
                }
            });

            return;
        }

        Schema::table('shipping_tiers', function (Blueprint $table) {
            if (Schema::hasColumn('shipping_tiers', 'tax_percentage')) {
                $table->dropColumn('tax_percentage');
            }

            if (! Schema::hasColumn('shipping_tiers', 'tax_id')) {
                $table->foreignId('tax_id')
                    ->after('cost_gross')
                    ->constrained('taxes');
            }
        });
    }

    public function down(): void
    {
        // If the table doesn't exist, nothing to do.
        if (! Schema::hasTable('shipping_tiers')) {
            return;
        }

        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            Schema::table('shipping_tiers', function (Blueprint $table) {
                if (! Schema::hasColumn('shipping_tiers', 'tax_percentage')) {
                    $table->decimal('tax_percentage', 5, 2)->nullable();
                }

                // Dropping a column on SQLite is not supported here — make tax_id nullable instead
                if (Schema::hasColumn('shipping_tiers', 'tax_id')) {
                    $table->unsignedBigInteger('tax_id')->nullable()->change();
                }
            });

            return;
        }

        Schema::table('shipping_tiers', function (Blueprint $table) {
            if (! Schema::hasColumn('shipping_tiers', 'tax_percentage')) {
                $table->decimal('tax_percentage', 5, 2);
            }

            if (Schema::hasColumn('shipping_tiers', 'tax_id')) {
                $table->dropForeign(['tax_id']);
                $table->dropColumn('tax_id');
            }
        });
    }
};
