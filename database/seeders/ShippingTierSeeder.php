<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ShippingTierSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('region_shipping_tier')->truncate();
        DB::table('country_shipping_tier')->truncate();
        DB::table('shipping_tier_translations')->truncate();
        DB::table('shipping_tiers')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // CTT standard (3-day mainland, 5-day islands): IDs 3–8
        // CTT express (1-day mainland, 3-day islands): IDs 9–14
        DB::table('shipping_tiers')->insert([
            // --- CTT Standard – Mainland (3 days) ---
            [
                'id' => 3,
                'weight_from' => 0,
                'weight_to' => 200,
                'cost_gross' => '3.00',
                'shipping_days' => 3,
                'tax_id' => 1,
                'active' => true,
                'created_at' => '2026-02-23 17:42:53',
                'updated_at' => '2026-02-23 17:42:53',
            ],
            [
                'id' => 4,
                'weight_from' => 201,
                'weight_to' => 1000,
                'cost_gross' => '5.00',
                'shipping_days' => 3,
                'tax_id' => 1,
                'active' => true,
                'created_at' => '2026-02-23 17:43:04',
                'updated_at' => '2026-02-23 17:44:04',
            ],
            [
                'id' => 5,
                'weight_from' => 1001,
                'weight_to' => 2000,
                'cost_gross' => '7.00',
                'shipping_days' => 3,
                'tax_id' => 1,
                'active' => true,
                'created_at' => '2026-02-23 17:44:09',
                'updated_at' => '2026-02-23 17:44:39',
            ],
            // --- CTT Standard – Islands (5 days) ---
            [
                'id' => 6,
                'weight_from' => 0,
                'weight_to' => 200,
                'cost_gross' => '3.00',
                'shipping_days' => 5,
                'tax_id' => 1,
                'active' => true,
                'created_at' => '2026-02-23 17:44:45',
                'updated_at' => '2026-02-23 17:45:01',
            ],
            [
                'id' => 7,
                'weight_from' => 201,
                'weight_to' => 1000,
                'cost_gross' => '5.00',
                'shipping_days' => 5,
                'tax_id' => 1,
                'active' => true,
                'created_at' => '2026-02-23 17:45:07',
                'updated_at' => '2026-02-23 17:45:19',
            ],
            [
                'id' => 8,
                'weight_from' => 1001,
                'weight_to' => 2000,
                'cost_gross' => '7.00',
                'shipping_days' => 5,
                'tax_id' => 1,
                'active' => true,
                'created_at' => '2026-02-23 17:45:22',
                'updated_at' => '2026-02-23 17:45:40',
            ],
            // --- CTT Express – Mainland (1 day) ---
            [
                'id' => 9,
                'weight_from' => 0,
                'weight_to' => 200,
                'cost_gross' => '5.00',
                'shipping_days' => 1,
                'tax_id' => 1,
                'active' => true,
                'created_at' => '2026-02-25 03:51:38',
                'updated_at' => '2026-02-25 03:55:28',
            ],
            [
                'id' => 10,
                'weight_from' => 201,
                'weight_to' => 1000,
                'cost_gross' => '7.00',
                'shipping_days' => 1,
                'tax_id' => 1,
                'active' => true,
                'created_at' => '2026-02-25 03:55:46',
                'updated_at' => '2026-02-25 03:56:04',
            ],
            [
                'id' => 11,
                'weight_from' => 1001,
                'weight_to' => 2000,
                'cost_gross' => '10.00',
                'shipping_days' => 1,
                'tax_id' => 1,
                'active' => true,
                'created_at' => '2026-02-25 03:57:22',
                'updated_at' => '2026-02-25 03:57:40',
            ],
            // --- CTT Express – Islands (3 days) ---
            [
                'id' => 12,
                'weight_from' => 0,
                'weight_to' => 200,
                'cost_gross' => '5.00',
                'shipping_days' => 3,
                'tax_id' => 1,
                'active' => true,
                'created_at' => '2026-02-25 03:58:19',
                'updated_at' => '2026-02-25 03:58:34',
            ],
            [
                'id' => 13,
                'weight_from' => 201,
                'weight_to' => 1000,
                'cost_gross' => '7.00',
                'shipping_days' => 3,
                'tax_id' => 1,
                'active' => true,
                'created_at' => '2026-02-25 03:58:39',
                'updated_at' => '2026-02-25 03:58:49',
            ],
            [
                'id' => 14,
                'weight_from' => 1001,
                'weight_to' => 2000,
                'cost_gross' => '10.00',
                'shipping_days' => 3,
                'tax_id' => 1,
                'active' => true,
                'created_at' => '2026-02-25 03:58:52',
                'updated_at' => '2026-02-25 03:59:03',
            ],
        ]);

        // Translations
        DB::table('shipping_tier_translations')->insert([
            // CTT Standard
            ['shipping_tier_id' => 3,  'locale' => 'pt-PT', 'name' => 'CTT'],
            ['shipping_tier_id' => 3,  'locale' => 'en-UK', 'name' => 'CTT'],
            ['shipping_tier_id' => 4,  'locale' => 'pt-PT', 'name' => 'CTT'],
            ['shipping_tier_id' => 4,  'locale' => 'en-UK', 'name' => 'CTT'],
            ['shipping_tier_id' => 5,  'locale' => 'pt-PT', 'name' => 'CTT'],
            ['shipping_tier_id' => 5,  'locale' => 'en-UK', 'name' => 'CTT'],
            ['shipping_tier_id' => 6,  'locale' => 'pt-PT', 'name' => 'CTT'],
            ['shipping_tier_id' => 6,  'locale' => 'en-UK', 'name' => 'CTT'],
            ['shipping_tier_id' => 7,  'locale' => 'pt-PT', 'name' => 'CTT'],
            ['shipping_tier_id' => 7,  'locale' => 'en-UK', 'name' => 'CTT'],
            ['shipping_tier_id' => 8,  'locale' => 'pt-PT', 'name' => 'CTT'],
            ['shipping_tier_id' => 8,  'locale' => 'en-UK', 'name' => 'CTT'],
            // CTT Express
            ['shipping_tier_id' => 9,  'locale' => 'pt-PT', 'name' => 'CTT EXPRESSO'],
            ['shipping_tier_id' => 9,  'locale' => 'en-UK', 'name' => 'CTT EXPRESS'],
            ['shipping_tier_id' => 10, 'locale' => 'pt-PT', 'name' => 'CTT EXPRESSO'],
            ['shipping_tier_id' => 10, 'locale' => 'en-UK', 'name' => 'CTT EXPRESS'],
            ['shipping_tier_id' => 11, 'locale' => 'pt-PT', 'name' => 'CTT EXPRESSO'],
            ['shipping_tier_id' => 11, 'locale' => 'en-UK', 'name' => 'CTT EXPRESS'],
            ['shipping_tier_id' => 12, 'locale' => 'pt-PT', 'name' => 'CTT EXPRESSO'],
            ['shipping_tier_id' => 12, 'locale' => 'en-UK', 'name' => 'CTT EXPRESS'],
            ['shipping_tier_id' => 13, 'locale' => 'pt-PT', 'name' => 'CTT EXPRESSO'],
            ['shipping_tier_id' => 13, 'locale' => 'en-UK', 'name' => 'CTT EXPRESS'],
            ['shipping_tier_id' => 14, 'locale' => 'pt-PT', 'name' => 'CTT EXPRESSO'],
            ['shipping_tier_id' => 14, 'locale' => 'en-UK', 'name' => 'CTT EXPRESS'],
        ]);

        // Country associations (all tiers → Portugal)
        DB::table('country_shipping_tier')->insert([
            ['shipping_tier_id' => 3,  'country_id' => 142],
            ['shipping_tier_id' => 4,  'country_id' => 142],
            ['shipping_tier_id' => 5,  'country_id' => 142],
            ['shipping_tier_id' => 6,  'country_id' => 142],
            ['shipping_tier_id' => 7,  'country_id' => 142],
            ['shipping_tier_id' => 8,  'country_id' => 142],
            ['shipping_tier_id' => 9,  'country_id' => 142],
            ['shipping_tier_id' => 10, 'country_id' => 142],
            ['shipping_tier_id' => 11, 'country_id' => 142],
            ['shipping_tier_id' => 12, 'country_id' => 142],
            ['shipping_tier_id' => 13, 'country_id' => 142],
            ['shipping_tier_id' => 14, 'country_id' => 142],
        ]);

        // Region associations
        // Mainland (region 1): standard tiers 3–5 (default: 3), express tiers 9–11
        // Azores   (region 2): standard tiers 6–8 (default: 6), express tiers 12–14
        // Madeira  (region 3): standard tiers 6–8 (default: 6), express tiers 12–14
        DB::table('region_shipping_tier')->insert([
            // Mainland – CTT Standard
            ['shipping_tier_id' => 3,  'region_id' => 1, 'is_default' => true],
            ['shipping_tier_id' => 4,  'region_id' => 1, 'is_default' => false],
            ['shipping_tier_id' => 5,  'region_id' => 1, 'is_default' => false],
            // Azores – CTT Standard
            ['shipping_tier_id' => 6,  'region_id' => 2, 'is_default' => true],
            ['shipping_tier_id' => 7,  'region_id' => 2, 'is_default' => false],
            ['shipping_tier_id' => 8,  'region_id' => 2, 'is_default' => false],
            // Madeira – CTT Standard
            ['shipping_tier_id' => 6,  'region_id' => 3, 'is_default' => true],
            ['shipping_tier_id' => 7,  'region_id' => 3, 'is_default' => false],
            ['shipping_tier_id' => 8,  'region_id' => 3, 'is_default' => false],
            // Mainland – CTT Express
            ['shipping_tier_id' => 9,  'region_id' => 1, 'is_default' => false],
            ['shipping_tier_id' => 10, 'region_id' => 1, 'is_default' => false],
            ['shipping_tier_id' => 11, 'region_id' => 1, 'is_default' => false],
            // Azores – CTT Express
            ['shipping_tier_id' => 12, 'region_id' => 2, 'is_default' => false],
            ['shipping_tier_id' => 13, 'region_id' => 2, 'is_default' => false],
            ['shipping_tier_id' => 14, 'region_id' => 2, 'is_default' => false],
            // Madeira – CTT Express
            ['shipping_tier_id' => 12, 'region_id' => 3, 'is_default' => false],
            ['shipping_tier_id' => 13, 'region_id' => 3, 'is_default' => false],
            ['shipping_tier_id' => 14, 'region_id' => 3, 'is_default' => false],
        ]);
    }
}
