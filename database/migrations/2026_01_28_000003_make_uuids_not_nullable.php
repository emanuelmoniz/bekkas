<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Make products.uuid NOT NULL
        if (Schema::hasTable('products') && Schema::hasColumn('products', 'uuid')) {
            $driver = DB::getDriverName();

            if ($driver === 'mysql') {
                DB::statement('ALTER TABLE `products` MODIFY `uuid` VARCHAR(36) NOT NULL UNIQUE;');
            } else {
                // Use change() where supported (requires doctrine/dbal in some environments)
                Schema::table('products', function (Blueprint $table) {
                    $table->string('uuid', 36)->nullable(false)->change();
                });
            }
        }

        // Make orders.uuid NOT NULL
        if (Schema::hasTable('orders') && Schema::hasColumn('orders', 'uuid')) {
            $driver = DB::getDriverName();

            if ($driver === 'mysql') {
                DB::statement('ALTER TABLE `orders` MODIFY `uuid` VARCHAR(36) NOT NULL UNIQUE;');
            } else {
                Schema::table('orders', function (Blueprint $table) {
                    $table->string('uuid', 36)->nullable(false)->change();
                });
            }
        }

        // Ensure tickets.uuid is NOT NULL and unique (should already be), but enforce if possible
        if (Schema::hasTable('tickets') && Schema::hasColumn('tickets', 'uuid')) {
            $driver = DB::getDriverName();

            if ($driver === 'mysql') {
                DB::statement('ALTER TABLE `tickets` MODIFY `uuid` VARCHAR(36) NOT NULL UNIQUE;');
            } else {
                Schema::table('tickets', function (Blueprint $table) {
                    $table->uuid('uuid')->nullable(false)->change();
                });
            }
        }
    }

    public function down(): void
    {
        // Revert to nullable where applicable
        if (Schema::hasTable('products') && Schema::hasColumn('products', 'uuid')) {
            $driver = DB::getDriverName();
            if ($driver === 'mysql') {
                DB::statement('ALTER TABLE `products` MODIFY `uuid` VARCHAR(36) NULL;');
            } else {
                Schema::table('products', function (Blueprint $table) {
                    $table->string('uuid', 36)->nullable()->change();
                });
            }
        }

        if (Schema::hasTable('orders') && Schema::hasColumn('orders', 'uuid')) {
            $driver = DB::getDriverName();
            if ($driver === 'mysql') {
                DB::statement('ALTER TABLE `orders` MODIFY `uuid` VARCHAR(36) NULL;');
            } else {
                Schema::table('orders', function (Blueprint $table) {
                    $table->string('uuid', 36)->nullable()->change();
                });
            }
        }

        if (Schema::hasTable('tickets') && Schema::hasColumn('tickets', 'uuid')) {
            $driver = DB::getDriverName();
            if ($driver === 'mysql') {
                DB::statement('ALTER TABLE `tickets` MODIFY `uuid` VARCHAR(36) NULL;');
            } else {
                Schema::table('tickets', function (Blueprint $table) {
                    $table->uuid('uuid')->nullable()->change();
                });
            }
        }
    }
};
