<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TicketCategoryTranslationSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('ticket_category_translations')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        DB::table('ticket_category_translations')->insert([
            ['id' => 10, 'ticket_category_id' => 5,  'locale' => 'en-UK', 'name' => 'Quote'],
            ['id' => 11, 'ticket_category_id' => 5,  'locale' => 'pt-PT', 'name' => 'Orçamento'],
            ['id' => 12, 'ticket_category_id' => 6,  'locale' => 'en-UK', 'name' => 'New Products'],
            ['id' => 13, 'ticket_category_id' => 6,  'locale' => 'pt-PT', 'name' => 'Novos Produtos'],
            ['id' => 14, 'ticket_category_id' => 7,  'locale' => 'en-UK', 'name' => 'Product Personalizations'],
            ['id' => 15, 'ticket_category_id' => 7,  'locale' => 'pt-PT', 'name' => 'Personalização de Produtos'],
            ['id' => 16, 'ticket_category_id' => 8,  'locale' => 'en-UK', 'name' => 'Orders'],
            ['id' => 17, 'ticket_category_id' => 8,  'locale' => 'pt-PT', 'name' => 'Encomendas'],
            ['id' => 18, 'ticket_category_id' => 9,  'locale' => 'en-UK', 'name' => 'Payments'],
            ['id' => 19, 'ticket_category_id' => 9,  'locale' => 'pt-PT', 'name' => 'Pagamentos'],
            ['id' => 20, 'ticket_category_id' => 10, 'locale' => 'en-UK', 'name' => 'Other Questions'],
            ['id' => 21, 'ticket_category_id' => 10, 'locale' => 'pt-PT', 'name' => 'Outras Questões'],
        ]);
    }
}
