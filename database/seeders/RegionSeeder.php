<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RegionSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('region_translations')->truncate();
        DB::table('regions')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        DB::table('regions')->insert([
            [
                'id'               => 1,
                'country_id'       => 142,
                'postal_code_from' => '1000-001',
                'postal_code_to'   => '8999-999',
                'is_active'        => true,
                'created_at'       => '2026-02-23 17:35:53',
                'updated_at'       => '2026-02-23 17:35:53',
            ],
            [
                'id'               => 2,
                'country_id'       => 142,
                'postal_code_from' => '9500-001',
                'postal_code_to'   => '9999-999',
                'is_active'        => true,
                'created_at'       => '2026-02-23 17:36:46',
                'updated_at'       => '2026-02-23 17:36:46',
            ],
            [
                'id'               => 3,
                'country_id'       => 142,
                'postal_code_from' => '9000-001',
                'postal_code_to'   => '9499-999',
                'is_active'        => true,
                'created_at'       => '2026-02-23 17:37:04',
                'updated_at'       => '2026-02-23 17:37:04',
            ],
        ]);

        DB::table('region_translations')->insert([
            // Mainland
            ['region_id' => 1, 'locale' => 'pt-PT', 'name' => 'CONTINENTE'],
            ['region_id' => 1, 'locale' => 'en-UK', 'name' => 'MAINLAND'],
            // Azores
            ['region_id' => 2, 'locale' => 'pt-PT', 'name' => 'AÇORES'],
            ['region_id' => 2, 'locale' => 'en-UK', 'name' => 'AZORES'],
            // Madeira
            ['region_id' => 3, 'locale' => 'pt-PT', 'name' => 'MADEIRA'],
            ['region_id' => 3, 'locale' => 'en-UK', 'name' => 'MADEIRA'],
        ]);
    }
}
