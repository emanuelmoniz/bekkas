<?php

use App\Models\Tax;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * This migration will:
     *  - ensure `products.tax_id` exists (nullable)
     *  - for each product with a non-null `tax` string, find or create
     *    a `taxes` record with that percentage and assign `tax_id`
     *  - drop the denormalized `products.tax` column
     *
     * Be conservative: guards with Schema::hasColumn and chunking.
     */
    public function up(): void
    {
        // Add tax_id column if missing
        if (! Schema::hasColumn('products', 'tax_id')) {
            Schema::table('products', function (Blueprint $table) {
                $table->foreignId('tax_id')->nullable()->after('price')
                    ->constrained('taxes')->nullOnDelete();
            });
        }

        // Migrate denormalized `tax` values into `taxes` table
        if (Schema::hasColumn('products', 'tax')) {
            DB::table('products')
                ->whereNotNull('tax')
                ->orderBy('id')
                ->chunk(100, function ($rows) {
                    foreach ($rows as $row) {
                        $raw = trim((string) ($row->tax ?? ''));
                        if ($raw === '' || ! is_numeric($raw)) {
                            continue;
                        }

                        $percentage = (float) $raw;

                        // Try find existing tax with same percentage
                        $tax = Tax::where('percentage', $percentage)->first();

                        if (! $tax) {
                            $tax = Tax::create([
                                'name' => "Migrated {$percentage}%",
                                'percentage' => $percentage,
                                'is_active' => true,
                            ]);
                        }

                        DB::table('products')
                            ->where('id', $row->id)
                            ->update(['tax_id' => $tax->id]);
                    }
                });

            // After migrating values, drop the denormalized column
            Schema::table('products', function (Blueprint $table) {
                if (Schema::hasColumn('products', 'tax')) {
                    $table->dropColumn('tax');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * Recreates `products.tax` and, where possible, copies back the
     * percentage from `taxes` (for products with `tax_id`). Then drops
     * the `tax_id` column.
     */
    public function down(): void
    {
        if (! Schema::hasColumn('products', 'tax')) {
            Schema::table('products', function (Blueprint $table) {
                $table->decimal('tax', 5, 2)->nullable()->after('price');
            });
        }

        DB::table('products')
            ->whereNotNull('tax_id')
            ->orderBy('id')
            ->chunk(100, function ($rows) {
                foreach ($rows as $row) {
                    $tax = DB::table('taxes')->where('id', $row->tax_id)->first();
                    $pct = $tax->percentage ?? null;
                    DB::table('products')
                        ->where('id', $row->id)
                        ->update(['tax' => $pct]);
                }
            });

        // Drop foreign key and tax_id column if present
        if (Schema::hasColumn('products', 'tax_id')) {
            Schema::table('products', function (Blueprint $table) {
                try {
                    $table->dropForeign(['tax_id']);
                } catch (\Exception $e) {
                    // ignore if the FK does not exist
                }

                try {
                    $table->dropColumn('tax_id');
                } catch (\Exception $e) {
                    // ignore if it fails
                }
            });
        }
    }
};
