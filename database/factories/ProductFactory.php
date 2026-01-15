<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Product;
use App\Models\Tax;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition()
    {
        return [
            'tax_id' => Tax::factory(),
            'price' => $this->faker->randomFloat(2, 5, 200),
            'promo_price' => null,
            'stock' => $this->faker->numberBetween(0, 10),
            'weight' => $this->faker->randomFloat(2, 0.1, 5),
            'active' => true,
            'is_backorder' => false,
            'production_time' => 0,
        ];
    }
}
