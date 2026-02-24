<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * The existing `path` column stores the 1000px thumbnail used in scrollers.
     * `original_path` stores the full-resolution original for future gallery use.
     */
    public function up(): void
    {
        Schema::table('product_photos', function (Blueprint $table) {
            $table->string('original_path')->nullable()->after('path');
        });

        Schema::table('project_photos', function (Blueprint $table) {
            $table->string('original_path')->nullable()->after('path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_photos', function (Blueprint $table) {
            $table->dropColumn('original_path');
        });

        Schema::table('project_photos', function (Blueprint $table) {
            $table->dropColumn('original_path');
        });
    }
};
