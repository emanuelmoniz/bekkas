<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('countries', function (Blueprint $table) {
            if (! Schema::hasColumn('countries', 'name_pt')) {
                $table->string('name_pt')->nullable()->after('id');
            }

            if (! Schema::hasColumn('countries', 'name_en')) {
                $table->string('name_en')->nullable()->after('name_pt');
            }
        });
    }

    public function down(): void
    {
        Schema::table('countries', function (Blueprint $table) {
            if (Schema::hasColumn('countries', 'name_en')) {
                $table->dropColumn('name_en');
            }

            if (Schema::hasColumn('countries', 'name_pt')) {
                $table->dropColumn('name_pt');
            }
        });
    }
};
