<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('region_default_shipping_tiers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('region_id')->constrained('regions')->onDelete('cascade');
            $table->foreignId('shipping_tier_id')->constrained('shipping_tiers')->onDelete('cascade');
            $table->timestamps();

            // Each region can have only one default shipping tier
            $table->unique('region_id');

            // Index for faster lookups
            $table->index('shipping_tier_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('region_default_shipping_tiers');
    }
};
