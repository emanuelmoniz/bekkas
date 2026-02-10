<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up()
    {
        \DB::table('shipping_configs')->insert([
            'key' => 'tracking_statuses',
            'value' => json_encode(['shipped', 'delivered']),
        ]);
    }

    public function down()
    {
        \DB::table('shipping_configs')->where('key', 'tracking_statuses')->delete();
    }
};
