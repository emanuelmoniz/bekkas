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
        DB::table('shipping_tiers')->truncate();
        DB::table('region_default_shipping_tiers')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        DB::table('shipping_tiers')->insert([
            [
                'id' => 1,
                'name_pt' => 'CTT-GRATUITO',
                'name_en' => 'CTT-FREE',
                'weight_from' => 0,
                'weight_to' => 2000,
                'cost_gross' => '0.00',
                'shipping_days' => 3,
                'tax_id' => 1,
                'active' => true,
                'is_default' => 0,
                'use_for_default' => true,
                'created_at' => '2026-02-23 17:38:55',
                'updated_at' => '2026-02-23 17:38:55',
            ],
            [
                'id' => 2,
                'name_pt' => 'CTT-GRATUITO',
                'name_en' => 'CTT-FREE',
                'weight_from' => 0,
                'weight_to' => 2000,
                'cost_gross' => '0.00',
                'shipping_days' => 5,
                'tax_id' => 1,
                'active' => true,
                'is_default' => 0,
                'use_for_default' => false,
                'created_at' => '2026-02-23 17:41:51',
                'updated_at' => '2026-02-23 17:42:08',
            ],
            [
                'id' => 3,
                'name_pt' => 'CTT',
                'name_en' => 'CTT',
                'weight_from' => 0,
                'weight_to' => 200,
                'cost_gross' => '3.00',
                'shipping_days' => 3,
                'tax_id' => 1,
                'active' => true,
                'is_default' => 0,
                'use_for_default' => false,
                'created_at' => '2026-02-23 17:42:53',
                'updated_at' => '2026-02-23 17:42:53',
            ],
            [
                'id' => 4,
                'name_pt' => 'CTT',
                'name_en' => 'CTT',
                'weight_from' => 201,
                'weight_to' => 1000,
                'cost_gross' => '5.00',
                'shipping_days' => 3,
                'tax_id' => 1,
                'active' => true,
                'is_default' => 0,
                'use_for_default' => false,
                'created_at' => '2026-02-23 17:43:04',
                'updated_at' => '2026-02-23 17:44:04',
            ],
            [
                'id' => 5,
                'name_pt' => 'CTT',
                'name_en' => 'CTT',
                'weight_from' => 1001,
                'weight_to' => 2000,
                'cost_gross' => '7.00',
                'shipping_days' => 3,
                'tax_id' => 1,
                'active' => true,
                'is_default' => 0,
                'use_for_default' => false,
                'created_at' => '2026-02-23 17:44:09',
                'updated_at' => '2026-02-23 17:44:39',
            ],
            [
                'id' => 6,
                'name_pt' => 'CTT',
                'name_en' => 'CTT',
                'weight_from' => 0,
                'weight_to' => 200,
                'cost_gross' => '3.00',
                'shipping_days' => 5,
                'tax_id' => 1,
                'active' => true,
                'is_default' => 0,
                'use_for_default' => false,
                'created_at' => '2026-02-23 17:44:45',
                'updated_at' => '2026-02-23 17:45:01',
            ],
            [
                'id' => 7,
                'name_pt' => 'CTT',
                'name_en' => 'CTT',
                'weight_from' => 201,
                'weight_to' => 1000,
                'cost_gross' => '5.00',
                'shipping_days' => 5,
                'tax_id' => 1,
                'active' => true,
                'is_default' => 0,
                'use_for_default' => false,
                'created_at' => '2026-02-23 17:45:07',
                'updated_at' => '2026-02-23 17:45:19',
            ],
            [
                'id' => 8,
                'name_pt' => 'CTT',
                'name_en' => 'CTT',
                'weight_from' => 1001,
                'weight_to' => 2000,
                'cost_gross' => '7.00',
                'shipping_days' => 5,
                'tax_id' => 1,
                'active' => true,
                'is_default' => 0,
                'use_for_default' => false,
                'created_at' => '2026-02-23 17:45:22',
                'updated_at' => '2026-02-23 17:45:40',
            ],
        ]);

        DB::table('country_shipping_tier')->insert([
            ['shipping_tier_id' => 1, 'country_id' => 142],
            ['shipping_tier_id' => 2, 'country_id' => 142],
            ['shipping_tier_id' => 3, 'country_id' => 142],
            ['shipping_tier_id' => 4, 'country_id' => 142],
            ['shipping_tier_id' => 5, 'country_id' => 142],
            ['shipping_tier_id' => 6, 'country_id' => 142],
            ['shipping_tier_id' => 7, 'country_id' => 142],
            ['shipping_tier_id' => 8, 'country_id' => 142],
        ]);

        DB::table('region_shipping_tier')->insert([
            ['shipping_tier_id' => 1, 'region_id' => 1],
            ['shipping_tier_id' => 2, 'region_id' => 2],
            ['shipping_tier_id' => 2, 'region_id' => 3],
            ['shipping_tier_id' => 3, 'region_id' => 1],
            ['shipping_tier_id' => 4, 'region_id' => 1],
            ['shipping_tier_id' => 5, 'region_id' => 1],
            ['shipping_tier_id' => 6, 'region_id' => 2],
            ['shipping_tier_id' => 6, 'region_id' => 3],
            ['shipping_tier_id' => 7, 'region_id' => 2],
            ['shipping_tier_id' => 7, 'region_id' => 3],
            ['shipping_tier_id' => 8, 'region_id' => 2],
            ['shipping_tier_id' => 8, 'region_id' => 3],
        ]);
    }
}
