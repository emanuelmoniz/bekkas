<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_option_types', function (Blueprint $table) {
            $table->boolean('have_stock')->default(false)->after('is_active');
            $table->boolean('have_price')->default(false)->after('have_stock');
        });
    }

    public function down(): void
    {
        Schema::table('product_option_types', function (Blueprint $table) {
            $table->dropColumn(['have_stock', 'have_price']);
        });
    }
};
