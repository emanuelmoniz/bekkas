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
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('dimensions');
            $table->unsignedInteger('width')->nullable()->after('execution_time');
            $table->unsignedInteger('length')->nullable()->after('width');
            $table->unsignedInteger('height')->nullable()->after('length');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['width', 'length', 'height']);
            $table->text('dimensions')->nullable()->after('execution_time');
        });
    }
};
