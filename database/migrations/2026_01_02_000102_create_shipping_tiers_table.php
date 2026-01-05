<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipping_tiers', function (Blueprint $table) {
            $table->id();

            // Weight block (grams)
            $table->unsignedInteger('weight_from');
            $table->unsignedInteger('weight_to');

            // Cost & tax (gross)
            $table->decimal('cost_gross', 10, 2);
            $table->decimal('tax_percentage', 5, 2);

            $table->boolean('active')->default(true);

            $table->timestamps();

            $table->index(['weight_from', 'weight_to', 'active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipping_tiers');
    }
};
