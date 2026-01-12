<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('shipping_tier_name')->nullable()->after('shipping_gross');
            $table->string('tracking_url')->nullable()->after('tracking_number');
            $table->date('expected_delivery_date')->nullable()->after('tracking_url');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['shipping_tier_name', 'tracking_url', 'expected_delivery_date']);
        });
    }
};
