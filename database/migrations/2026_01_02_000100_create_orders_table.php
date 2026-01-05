<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            // Relations
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('address_id')->constrained('addresses');

            // Status & flags
            $table->string('status')->default('PROCESSING');
            $table->boolean('is_paid')->default(false);
            $table->boolean('is_canceled')->default(false);
            $table->boolean('is_refunded')->default(false);

            // Admin data
            $table->string('tracking_number')->nullable();

            // Products totals
            $table->decimal('products_total_net', 10, 2);
            $table->decimal('products_total_tax', 10, 2);
            $table->decimal('products_total_gross', 10, 2);

            // Shipping totals
            $table->decimal('shipping_net', 10, 2);
            $table->decimal('shipping_tax', 10, 2);
            $table->decimal('shipping_gross', 10, 2);

            // Order totals
            $table->decimal('total_net', 10, 2);
            $table->decimal('total_tax', 10, 2);
            $table->decimal('total_gross', 10, 2);

            $table->timestamps();

            $table->index(['status', 'is_paid', 'is_canceled', 'is_refunded']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
