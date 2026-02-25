<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TicketCategorySeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('ticket_category_translations')->truncate();
        DB::table('ticket_categories')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        DB::table('ticket_categories')->insert([
            ['id' => 5,  'active' => 1, 'created_at' => '2026-02-25 19:40:04', 'updated_at' => '2026-02-25 19:40:04'],
            ['id' => 6,  'active' => 1, 'created_at' => '2026-02-25 19:40:33', 'updated_at' => '2026-02-25 19:40:33'],
            ['id' => 7,  'active' => 1, 'created_at' => '2026-02-25 19:40:58', 'updated_at' => '2026-02-25 19:40:58'],
            ['id' => 8,  'active' => 1, 'created_at' => '2026-02-25 19:41:39', 'updated_at' => '2026-02-25 19:41:39'],
            ['id' => 9,  'active' => 1, 'created_at' => '2026-02-25 19:41:53', 'updated_at' => '2026-02-25 19:41:53'],
            ['id' => 10, 'active' => 1, 'created_at' => '2026-02-25 19:42:10', 'updated_at' => '2026-02-25 19:42:10'],
        ]);
    }
}
