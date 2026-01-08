<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('addresses', function (Blueprint $table) {
            // Remove old country text column
            $table->dropColumn('country');
        });
    }

    public function down(): void
    {
        Schema::table('addresses', function (Blueprint $table) {
            // Restore old country text column
            $table->string('country')->after('city');
        });
    }
};
