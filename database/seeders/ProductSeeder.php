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
                'width' => null,
                'length' => null,
                'height' => null,
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
                'width' => null,
                'length' => null,
                'height' => null,
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
                'width' => null,
                'length' => null,
                'height' => null,
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
            ['id' => 1, 'product_id' => 1, 'locale' => 'pt-PT', 'name' => 'Candeiro Lua', 'description' => null, 'technical_info' => null],
            ['id' => 2, 'product_id' => 1, 'locale' => 'en-UK', 'name' => 'Moon Light', 'description' => null, 'technical_info' => null],
            ['id' => 3, 'product_id' => 2, 'locale' => 'pt-PT', 'name' => 'Suporte de Caneta', 'description' => null, 'technical_info' => null],
            ['id' => 4, 'product_id' => 2, 'locale' => 'en-UK', 'name' => 'Pen Holder', 'description' => null, 'technical_info' => null],
            ['id' => 5, 'product_id' => 3, 'locale' => 'pt-PT', 'name' => 'Vaso para Plantas', 'description' => null, 'technical_info' => null],
            ['id' => 6, 'product_id' => 3, 'locale' => 'en-UK', 'name' => 'Plant Vase', 'description' => null, 'technical_info' => null],
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
            ['id' => 1, 'product_id' => 1, 'path' => 'products/TAFWbXfZw2GWBjmh1uQwRHQFXX96sEkbpNqXQtMI.jpg', 'is_primary' => 1, 'created_at' => '2026-02-23 17:25:47', 'updated_at' => '2026-02-23 17:25:47'],
            ['id' => 2, 'product_id' => 1, 'path' => 'products/KK5cHTWMDDRJy1mKXUrZzqPgJDRFhCyGT7dfjxDf.jpg', 'is_primary' => 0, 'created_at' => '2026-02-23 17:25:47', 'updated_at' => '2026-02-23 17:25:47'],
            ['id' => 3, 'product_id' => 1, 'path' => 'products/eoEr7ByRDV8ST5E5HTBEquUgBEoECDt8UdB7Y3pY.jpg', 'is_primary' => 0, 'created_at' => '2026-02-23 17:25:47', 'updated_at' => '2026-02-23 17:25:47'],
            ['id' => 4, 'product_id' => 2, 'path' => 'products/iCozb1Y2L1wxIsD0jCoSEsH0gB1dU25BJK12gjNY.jpg', 'is_primary' => 0, 'created_at' => '2026-02-23 17:31:01', 'updated_at' => '2026-02-23 17:31:18'],
            ['id' => 5, 'product_id' => 2, 'path' => 'products/Es1y0VjkSILMcvSbKQIVI9bgPXbB9d7HkjXj7YFs.jpg', 'is_primary' => 0, 'created_at' => '2026-02-23 17:31:01', 'updated_at' => '2026-02-23 17:31:18'],
            ['id' => 6, 'product_id' => 2, 'path' => 'products/P32FQifeK2etfZI3JmSuTVqEftYowrlmWjNyMlGw.jpg', 'is_primary' => 0, 'created_at' => '2026-02-23 17:31:01', 'updated_at' => '2026-02-23 17:31:18'],
            ['id' => 7, 'product_id' => 2, 'path' => 'products/RL1J3XjmQr0XujmS45aCqP7yxEo2vlVNhchtFEk1.jpg', 'is_primary' => 0, 'created_at' => '2026-02-23 17:31:01', 'updated_at' => '2026-02-23 17:31:18'],
            ['id' => 8, 'product_id' => 2, 'path' => 'products/ztbV3bmVq8hy34Pvvg0RzBSp8p2iL9ctJ92yj0In.jpg', 'is_primary' => 1, 'created_at' => '2026-02-23 17:31:14', 'updated_at' => '2026-02-23 17:31:18'],
            ['id' => 9, 'product_id' => 2, 'path' => 'products/RpbaPr5208XTyUcOxdOHuJjJKm1D5M6Mmp2mgqLX.jpg', 'is_primary' => 0, 'created_at' => '2026-02-23 17:31:15', 'updated_at' => '2026-02-23 17:31:18'],
            ['id' => 10, 'product_id' => 3, 'path' => 'products/090Ezv1T7aBKeerqSKW65e9q9NGk8tW8InLXqDFA.jpg', 'is_primary' => 0, 'created_at' => '2026-02-23 17:34:54', 'updated_at' => '2026-02-23 17:34:59'],
            ['id' => 11, 'product_id' => 3, 'path' => 'products/XDYYrjB7PpWYqym5OiAYdvfsM1MUrlRPhxNIwAeT.jpg', 'is_primary' => 0, 'created_at' => '2026-02-23 17:34:55', 'updated_at' => '2026-02-23 17:34:59'],
            ['id' => 12, 'product_id' => 3, 'path' => 'products/HXEjfeRP5uxNL1FIiLxZcZDmoTPcupicjwKWVGlP.jpg', 'is_primary' => 0, 'created_at' => '2026-02-23 17:34:55', 'updated_at' => '2026-02-23 17:34:59'],
            ['id' => 13, 'product_id' => 3, 'path' => 'products/TFUsidoW7Gx6CkOkqoMy7mWzUBVxuCM8Kqi7LHsd.jpg', 'is_primary' => 0, 'created_at' => '2026-02-23 17:34:55', 'updated_at' => '2026-02-23 17:34:59'],
            ['id' => 14, 'product_id' => 3, 'path' => 'products/BO7jW50EkUr7PKnu3PQhejnsJYdUqOEonEsZKl50.jpg', 'is_primary' => 1, 'created_at' => '2026-02-23 17:34:55', 'updated_at' => '2026-02-23 17:34:59'],
        ]);
    }
}
