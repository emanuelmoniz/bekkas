<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

class UuidNotNullableTest extends TestCase
{
    use RefreshDatabase;

    public function test_products_and_orders_uuids_are_not_nullable()
    {
        // This test relies on the SQLite test DB (PRAGMA table_info)
        $productInfo = DB::select("PRAGMA table_info('products')");
        $orderInfo = DB::select("PRAGMA table_info('orders')");

        $find = function ($rows, $name) {
            foreach ($rows as $r) {
                if ($r->name === $name) return $r;
            }
            return null;
        };

        $productUuid = $find($productInfo, 'uuid');
        $orderUuid = $find($orderInfo, 'uuid');

        $this->assertNotNull($productUuid, 'products.uuid column should exist');
        $this->assertNotNull($orderUuid, 'orders.uuid column should exist');

        // PRAGMA table_info 'notnull' is 1 when NOT NULL is set
        $this->assertEquals(1, $productUuid->notnull, 'products.uuid should be NOT NULL');
        $this->assertEquals(1, $orderUuid->notnull, 'orders.uuid should be NOT NULL');
    }
}
