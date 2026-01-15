<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Order;

class OrderItemFactory extends Factory
{
    protected $model = OrderItem::class;

    public function definition()
    {
        $unitGross = $this->faker->randomFloat(2, 5, 200);
        $quantity = $this->faker->numberBetween(1, 3);
        $taxPerc = 23;
        $gross = round($unitGross * $quantity, 2);
        $net = round($gross / (1 + $taxPerc / 100), 2);
        $tax = round($gross - $net, 2);

        return [
            'order_id' => Order::factory(),
            'product_id' => Product::factory(),
            'quantity' => $quantity,
            'was_backordered' => false,
            'unit_price_gross' => $unitGross,
            'tax_percentage' => $taxPerc,
            'unit_weight' => 1.0,
            'total_net' => $net,
            'total_tax' => $tax,
            'total_gross' => $gross,
        ];
    }
}
