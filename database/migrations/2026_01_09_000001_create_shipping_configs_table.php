<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipping_configs', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('value')->nullable();
            $table->timestamps();
        });

        // Insert default configurations
        DB::table('shipping_configs')->insert([
            [
                'key' => 'free_shipping_over',
                'value' => '0',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'default_shipping_tier_id',
                'value' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('shipping_configs');
    }
};
