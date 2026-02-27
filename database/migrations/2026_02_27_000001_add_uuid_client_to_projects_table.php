<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('projects')) {
            return;
        }

        Schema::table('projects', function (Blueprint $table) {
            if (! Schema::hasColumn('projects', 'uuid')) {
                $table->string('uuid', 36)->nullable()->unique()->after('id');
            }
            if (! Schema::hasColumn('projects', 'client')) {
                $table->string('client')->nullable()->after('is_featured');
            }
            if (! Schema::hasColumn('projects', 'client_url')) {
                $table->string('client_url')->nullable()->after('client');
            }
        });

        // Backfill existing rows with UUIDs
        $projects = DB::table('projects')->whereNull('uuid')->select('id')->get();
        foreach ($projects as $project) {
            DB::table('projects')->where('id', $project->id)->update([
                'uuid' => (string) Str::uuid(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('projects')) {
            return;
        }

        Schema::table('projects', function (Blueprint $table) {
            if (Schema::hasColumn('projects', 'uuid')) {
                $table->dropUnique(['uuid']);
                $table->dropColumn('uuid');
            }
            if (Schema::hasColumn('projects', 'client')) {
                $table->dropColumn('client');
            }
            if (Schema::hasColumn('projects', 'client_url')) {
                $table->dropColumn('client_url');
            }
        });
    }
};
