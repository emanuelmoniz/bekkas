<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shipping_tiers', function (Blueprint $table) {
            $table->dropColumn('tax_percentage');

            $table->foreignId('tax_id')
                ->after('cost_gross')
                ->constrained('taxes');
        });
    }

    public function down(): void
    {
        Schema::table('shipping_tiers', function (Blueprint $table) {
            $table->decimal('tax_percentage', 5, 2);

            $table->dropForeign(['tax_id']);
            $table->dropColumn('tax_id');
        });
    }
};
