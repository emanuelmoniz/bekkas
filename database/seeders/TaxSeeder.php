<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TaxSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('tax_translations')->truncate();
        DB::table('taxes')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        DB::table('taxes')->insert([
            [
                'id'         => 1,
                'percentage' => '23.00',
                'is_active'  => true,
                'created_at' => '2026-02-23 17:10:58',
                'updated_at' => '2026-02-23 17:10:58',
            ],
        ]);

        DB::table('tax_translations')->insert([
            ['tax_id' => 1, 'locale' => 'pt-PT', 'name' => 'IVA'],
            ['tax_id' => 1, 'locale' => 'en-UK', 'name' => 'VAT'],
        ]);
    }
}
