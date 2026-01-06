<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Populate address snapshots for existing orders
        // This must run BEFORE adding the NOT NULL constraint
        $orders = DB::table('orders')
            ->join('addresses', 'orders.address_id', '=', 'addresses.id')
            ->select('orders.id as order_id', 'addresses.*')
            ->get();

        foreach ($orders as $order) {
            DB::table('orders')
                ->where('id', $order->order_id)
                ->update([
                    'address_title' => $order->title,
                    'address_nif' => $order->nif,
                    'address_line_1' => $order->address_line_1,
                    'address_line_2' => $order->address_line_2,
                    'address_postal_code' => $order->postal_code,
                    'address_city' => $order->city,
                    'address_country' => $order->country,
                ]);
        }
    }

    public function down(): void
    {
        // No need to revert data
    }
};
