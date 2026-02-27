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

        $now = now();

        DB::table('ticket_categories')->insert([
            ['slug' => 'rnd',         'active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['slug' => 'preparation', 'active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['slug' => 'print',       'active' => 1, 'created_at' => $now, 'updated_at' => $now],
        ]);
    }
}
