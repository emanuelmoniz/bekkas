<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('product_photos')->truncate();
        DB::table('category_product')->truncate();
        DB::table('material_product')->truncate();
        DB::table('product_translations')->truncate();
        DB::table('products')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        DB::table('products')->insert([
            [
                'id' => 1,
                'uuid' => '823f1687-a1ff-4a1f-af85-fc17cdbac088',
                'tax_id' => 1,
                'is_featured' => true,
                'is_promo' => false,
                'price' => '20.00',
                'promo_price' => '15.00',
                'width' => '120.00',
                'length' => '120.00',
                'height' => '140.00',
                'weight' => '200.000',
                'stock' => 10,
                'production_time' => 2,
                'is_backorder' => true,
                'active' => true,
                'created_at' => '2026-02-23 17:24:29',
                'updated_at' => '2026-02-23 17:24:29',
            ],
            [
                'id' => 2,
                'uuid' => '6d42492a-3ed8-4596-87ff-e1c95cb89a12',
                'tax_id' => 1,
                'is_featured' => true,
                'is_promo' => true,
                'price' => '3.00',
                'promo_price' => '2.00',
                'width' => '80.00',
                'length' => '8.00',
                'height' => '30.00',
                'weight' => '5.000',
                'stock' => 10,
                'production_time' => 1,
                'is_backorder' => true,
                'active' => true,
                'created_at' => '2026-02-23 17:30:07',
                'updated_at' => '2026-02-23 17:30:07',
            ],
            [
                'id' => 3,
                'uuid' => 'b0f5a3b1-b8fc-4937-8b40-4027f176240e',
                'tax_id' => 1,
                'is_featured' => false,
                'is_promo' => false,
                'price' => '10.00',
                'promo_price' => '8.00',
                'width' => '140.00',
                'length' => '140.00',
                'height' => '180.00',
                'weight' => '250.000',
                'stock' => 10,
                'production_time' => 2,
                'is_backorder' => true,
                'active' => true,
                'created_at' => '2026-02-23 17:34:20',
                'updated_at' => '2026-02-23 17:34:20',
            ],
        ]);

        DB::table('product_translations')->insert([
            ['id' => 1, 'product_id' => 1, 'locale' => 'pt-PT', 'name' => 'Candeiro Lua',
                'description' => "Pequeno candeeiro em forma de lua.\nInclui luz RGB e controlo remoto",
                'technical_info' => "Usa 2 pilhas tipo AA (não incluidas)"],
            ['id' => 2, 'product_id' => 1, 'locale' => 'en-UK', 'name' => 'Moon Light',
                'description' => "Small light with moon shell.\nIncludes RGB light and remote control.",
                'technical_info' => "Use 2 AA batteries (not included)"],
            ['id' => 3, 'product_id' => 2, 'locale' => 'pt-PT', 'name' => 'Suporte de Caneta',
                'description' => "Clip para livro ou caderno.\nSuporte para 1 caneta ou lapis, de forma redonda.\nPoder contactar-nos para solicitar outras versões.",
                'technical_info' => "O diâmetro do clip deverá ser igual ou inferior em 1mm ao diâmetro da caneta ou lapis."],
            ['id' => 4, 'product_id' => 2, 'locale' => 'en-UK', 'name' => 'Pen Holder',
                'description' => "Book or notebook clip.\nFor 1 pen or pencil with round shape.\nPlease contact us to request other versions.",
                'technical_info' => "Clip diameter should be equal or 1mm inferior of pen or pencil diameter."],
            ['id' => 5, 'product_id' => 3, 'locale' => 'pt-PT', 'name' => 'Vaso para Plantas',
                'description' => "Vaso com base removível para despejo de agua em excesso.",
                'technical_info' => "Zona da planta com fundo permeável de forma a escoar agua."],
            ['id' => 6, 'product_id' => 3, 'locale' => 'en-UK', 'name' => 'Plant Vase',
                'description' => "Removable base to clean up excess water.",
                'technical_info' => "Plant section as permeable bottom to remove excess water."],
        ]);

        DB::table('category_product')->insert([
            ['product_id' => 1, 'category_id' => 2],
            ['product_id' => 2, 'category_id' => 3],
            ['product_id' => 3, 'category_id' => 4],
        ]);

        DB::table('material_product')->insert([
            ['product_id' => 1, 'material_id' => 1],
            ['product_id' => 2, 'material_id' => 1],
            ['product_id' => 3, 'material_id' => 1],
        ]);

        DB::table('product_photos')->insert([
            ['id' => 26, 'product_id' => 1, 'path' => 'products/8O4TzrtiaI7EZRDfbRtBbprZsZw3SPIsytiW34LC.jpg', 'is_primary' => 1, 'created_at' => '2026-02-24 12:44:19', 'updated_at' => '2026-02-24 12:44:19'],
            ['id' => 27, 'product_id' => 1, 'path' => 'products/yGGaANnYvMWYnAZCIozO3MSTZPCY5x3lSJ9NWZNO.jpg', 'is_primary' => 0, 'created_at' => '2026-02-24 12:44:20', 'updated_at' => '2026-02-24 12:44:20'],
            ['id' => 28, 'product_id' => 1, 'path' => 'products/YYId1c1ZDtviYcf8NTSh7uv10YjxgC6EjYIZ6p7a.jpg', 'is_primary' => 0, 'created_at' => '2026-02-24 12:44:21', 'updated_at' => '2026-02-24 12:44:21'],
            ['id' => 21, 'product_id' => 2, 'path' => 'products/A0Jk9AIAIZglQC55o824pnw7cMPahEaWvRLpIUpf.jpg', 'is_primary' => 1, 'created_at' => '2026-02-24 12:41:51', 'updated_at' => '2026-02-24 12:41:51'],
            ['id' => 22, 'product_id' => 2, 'path' => 'products/q0BM6vfjEh1CNAvLsveJumOhCPyJmOH01UOoXzHT.jpg', 'is_primary' => 0, 'created_at' => '2026-02-24 12:41:52', 'updated_at' => '2026-02-24 12:41:52'],
            ['id' => 23, 'product_id' => 2, 'path' => 'products/pgYqfKGmVbsWVSRvFC2VDBVY5tN6ycs9UgrV9xYr.jpg', 'is_primary' => 0, 'created_at' => '2026-02-24 12:41:53', 'updated_at' => '2026-02-24 12:41:53'],
            ['id' => 24, 'product_id' => 2, 'path' => 'products/L0BQdh9Ma8W6Q0wvM1LNWnscZmUGSGT6sRXccUz0.jpg', 'is_primary' => 0, 'created_at' => '2026-02-24 12:41:53', 'updated_at' => '2026-02-24 12:41:53'],
            ['id' => 25, 'product_id' => 2, 'path' => 'products/LVePTeYVcxtdITL6v5RysXzIpAEeoab51BCp0UVW.jpg', 'is_primary' => 0, 'created_at' => '2026-02-24 12:41:54', 'updated_at' => '2026-02-24 12:41:54'],
            ['id' => 15, 'product_id' => 3, 'path' => 'products/FG0GH4BQKOZ722SpaoqN3R1vDkc55u4qCrzmcMNQ.jpg', 'is_primary' => 1, 'created_at' => '2026-02-24 12:39:18', 'updated_at' => '2026-02-24 12:39:18'],
            ['id' => 16, 'product_id' => 3, 'path' => 'products/28hqtzhqDytQTeDS3hgbjDa6sW9ZE4xTp5v3IMm9.jpg', 'is_primary' => 0, 'created_at' => '2026-02-24 12:39:19', 'updated_at' => '2026-02-24 12:39:19'],
            ['id' => 17, 'product_id' => 3, 'path' => 'products/oQt0rr5DVASYpNdCphIKCL4cEZcjDeJriRbDCiwy.jpg', 'is_primary' => 0, 'created_at' => '2026-02-24 12:39:20', 'updated_at' => '2026-02-24 12:39:20'],
            ['id' => 18, 'product_id' => 3, 'path' => 'products/F3HI5a2Da2s6dmpUIuJGAcTNtHxPygJGCcjjh9EJ.jpg', 'is_primary' => 0, 'created_at' => '2026-02-24 12:39:21', 'updated_at' => '2026-02-24 12:39:21'],
            ['id' => 19, 'product_id' => 3, 'path' => 'products/qN4YPHetXQ2fZ760kEvuCHRDcV1mA0VZI1vhgmTf.jpg', 'is_primary' => 0, 'created_at' => '2026-02-24 12:39:21', 'updated_at' => '2026-02-24 12:39:21'],
            ['id' => 20, 'product_id' => 3, 'path' => 'products/4iO9UQui08BpbLIgByVcIfHDHnOU2lOsN0gwuALE.jpg', 'is_primary' => 0, 'created_at' => '2026-02-24 12:39:22', 'updated_at' => '2026-02-24 12:39:22'],
        ]);
    }
}
