<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained();

            // Snapshots
            $table->unsignedInteger('quantity');
            $table->decimal('unit_price_gross', 10, 2);
            $table->decimal('tax_percentage', 5, 2);
            $table->unsignedInteger('unit_weight'); // grams

            // Calculated snapshots
            $table->decimal('total_net', 10, 2);
            $table->decimal('total_tax', 10, 2);
            $table->decimal('total_gross', 10, 2);

            $table->timestamps();

            $table->index('order_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
