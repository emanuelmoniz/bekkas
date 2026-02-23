<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RegionSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('regions')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        DB::table('regions')->insert([
            [
                'id' => 1,
                'country_id' => 142,
                'name' => 'MAINLAND',
                'postal_code_from' => '1000-001',
                'postal_code_to' => '8999-999',
                'is_active' => true,
                'created_at' => '2026-02-23 17:35:53',
                'updated_at' => '2026-02-23 17:35:53',
            ],
            [
                'id' => 2,
                'country_id' => 142,
                'name' => 'AZORES',
                'postal_code_from' => '9500-001',
                'postal_code_to' => '9999-999',
                'is_active' => true,
                'created_at' => '2026-02-23 17:36:46',
                'updated_at' => '2026-02-23 17:36:46',
            ],
            [
                'id' => 3,
                'country_id' => 142,
                'name' => 'MADEIRA',
                'postal_code_from' => '9000-001',
                'postal_code_to' => '9499-999',
                'is_active' => true,
                'created_at' => '2026-02-23 17:37:04',
                'updated_at' => '2026-02-23 17:37:04',
            ],
        ]);
    }
}
