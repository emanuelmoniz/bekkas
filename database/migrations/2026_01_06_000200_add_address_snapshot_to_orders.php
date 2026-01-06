<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Drop the foreign key constraint first
            $table->dropForeign(['address_id']);
            
            // Make address_id nullable (keep for reference but not enforced)
            $table->foreignId('address_id')->nullable()->change();
            
            // Add address snapshot columns
            $table->string('address_title')->after('address_id');
            $table->string('address_nif')->after('address_title');
            $table->string('address_line_1')->after('address_nif');
            $table->string('address_line_2')->nullable()->after('address_line_1');
            $table->string('address_postal_code')->after('address_line_2');
            $table->string('address_city')->after('address_postal_code');
            $table->string('address_country')->after('address_city');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Remove snapshot columns
            $table->dropColumn([
                'address_title',
                'address_nif',
                'address_line_1',
                'address_line_2',
                'address_postal_code',
                'address_city',
                'address_country',
            ]);
            
            // Restore foreign key constraint
            $table->foreignId('address_id')->change();
            $table->foreign('address_id')->references('id')->on('addresses');
        });
    }
};
