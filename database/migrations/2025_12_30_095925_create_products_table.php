<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            // renamed from `is_new` in a later migration; new installations use
            // `is_featured` directly.
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_promo')->default(false);

            $table->decimal('price', 10, 2);
            $table->decimal('promo_price', 10, 2)->nullable();
            $table->decimal('tax', 5, 2)->default(0);

            $table->decimal('width', 8, 2)->nullable();
            $table->decimal('length', 8, 2)->nullable();
            $table->decimal('height', 8, 2)->nullable();
            $table->decimal('weight', 8, 2)->nullable();

            $table->integer('stock')->default(0);
            $table->boolean('active')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
