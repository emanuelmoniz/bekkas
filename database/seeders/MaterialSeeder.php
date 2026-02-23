<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MaterialSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('material_translations')->truncate();
        DB::table('materials')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        DB::table('materials')->insert([
            ['id' => 1, 'created_at' => '2026-02-23 17:04:17', 'updated_at' => '2026-02-23 17:04:17'],
            ['id' => 2, 'created_at' => '2026-02-23 17:04:25', 'updated_at' => '2026-02-23 17:04:25'],
        ]);

        DB::table('material_translations')->insert([
            ['id' => 1, 'material_id' => 1, 'locale' => 'pt-PT', 'name' => 'PLA'],
            ['id' => 2, 'material_id' => 1, 'locale' => 'en-UK', 'name' => 'PLA'],
            ['id' => 3, 'material_id' => 2, 'locale' => 'pt-PT', 'name' => 'PETG'],
            ['id' => 4, 'material_id' => 2, 'locale' => 'en-UK', 'name' => 'PETG'],
        ]);
    }
}
