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
        Schema::table('configurations', function (Blueprint $table) {
            $table->text('address_line1')->nullable()->after('easypay_payment_methods');
            $table->text('address_line2')->nullable()->after('address_line1');
            $table->text('postal_code')->nullable()->after('address_line2');
            $table->foreignId('country_id')->nullable()->constrained('countries')->nullOnDelete()->after('postal_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('configurations', function (Blueprint $table) {
            $table->dropForeign(['country_id']);
            $table->dropColumn(['address_line1', 'address_line2', 'postal_code', 'country_id']);
        });
    }
};
