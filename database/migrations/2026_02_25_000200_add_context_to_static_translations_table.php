<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('static_translations', function (Blueprint $table) {
            // Human-readable description of WHERE this key is used (same across locales for a key).
            // Editable in the admin UI; the seeder only sets it on INSERT, never overwrites edits.
            $table->string('context', 255)->nullable()->after('locale');
        });
    }

    public function down(): void
    {
        Schema::table('static_translations', function (Blueprint $table) {
            $table->dropColumn('context');
        });
    }
};
