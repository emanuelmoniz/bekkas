<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('configurations', function (Blueprint $table) {
            $table->boolean('is_maintenance')->default(true)->after('tax_enabled');
            $table->text('maintenance_title')->default('BEKKAS IS IMPROVING')->after('is_maintenance');
            $table->text('maintenance_subtitle')->default('Everyday design will be even better!')->after('maintenance_title');
            $table->text('maintenance_text')->default('Our website is not available at the moment. We will try to be quick. Please come back soon.')->after('maintenance_subtitle');
        });
    }

    public function down(): void
    {
        Schema::table('configurations', function (Blueprint $table) {
            $table->dropColumn(['is_maintenance', 'maintenance_title', 'maintenance_subtitle', 'maintenance_text']);
        });
    }
};
