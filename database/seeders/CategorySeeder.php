<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        // clear existing rows (optional)
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('category_translations')->truncate();
        DB::table('categories')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $now = now();

        DB::table('categories')->insert([
            ['id' => 1, 'parent_id' => null, 'created_at' => '2026-02-23 17:00:35', 'updated_at' => '2026-02-23 17:00:35'],
            ['id' => 4, 'parent_id' => null, 'created_at' => '2026-02-23 17:03:34', 'updated_at' => '2026-02-23 17:03:34'],
            ['id' => 3, 'parent_id' => null, 'created_at' => '2026-02-23 17:03:19', 'updated_at' => '2026-02-23 17:03:19'],
            ['id' => 2, 'parent_id' => 4,    'created_at' => '2026-02-23 17:02:56', 'updated_at' => '2026-02-23 17:03:45'],
        ]);

        DB::table('category_translations')->insert([
            ['id' => 1, 'category_id' => 1, 'locale' => 'pt-PT', 'name' => 'Natal'],
            ['id' => 2, 'category_id' => 1, 'locale' => 'en-UK', 'name' => 'Christmas'],
            ['id' => 3, 'category_id' => 2, 'locale' => 'pt-PT', 'name' => 'Decoração'],
            ['id' => 4, 'category_id' => 2, 'locale' => 'en-UK', 'name' => 'Decoration'],
            ['id' => 5, 'category_id' => 3, 'locale' => 'pt-PT', 'name' => 'Organização'],
            ['id' => 6, 'category_id' => 3, 'locale' => 'en-UK', 'name' => 'Organizer'],
            ['id' => 7, 'category_id' => 4, 'locale' => 'pt-PT', 'name' => 'Casa'],
            ['id' => 8, 'category_id' => 4, 'locale' => 'en-UK', 'name' => 'Home'],
        ]);
    }
}
