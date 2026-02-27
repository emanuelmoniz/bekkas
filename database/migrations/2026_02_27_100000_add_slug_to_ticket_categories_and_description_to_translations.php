<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ticket_categories', function (Blueprint $table) {
            $table->string('slug')->nullable()->after('active');
        });

        Schema::table('ticket_category_translations', function (Blueprint $table) {
            $table->text('description')->nullable()->after('name');
        });

        // Add unique index on slug after column creation
        Schema::table('ticket_categories', function (Blueprint $table) {
            $table->unique('slug');
        });
    }

    public function down(): void
    {
        Schema::table('ticket_category_translations', function (Blueprint $table) {
            $table->dropColumn('description');
        });

        Schema::table('ticket_categories', function (Blueprint $table) {
            $table->dropUnique(['slug']);
            $table->dropColumn('slug');
        });
    }
};
