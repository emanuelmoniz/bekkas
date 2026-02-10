<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Update all addresses to use Portugal (PT) country_id
        $portugalId = DB::table('countries')->where('iso_alpha2', 'PT')->value('id');

        if ($portugalId) {
            DB::table('addresses')
                ->whereNotNull('country')
                ->update(['country_id' => $portugalId]);
        }
    }

    public function down(): void
    {
        // No need to reverse this data migration
    }
};
