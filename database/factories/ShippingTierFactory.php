<?php

namespace Database\Factories;

use App\Models\ShippingTier;
use App\Models\Tax;
use Illuminate\Database\Eloquent\Factories\Factory;

class ShippingTierFactory extends Factory
{
    protected $model = ShippingTier::class;

    public function definition()
    {
        return [
            'name_en' => 'Standard',
            'name_pt' => 'Standard',
            'tax_id' => Tax::factory(),
            'weight_from' => 0,
            'weight_to' => 9999,
            'cost_gross' => 5.00,
            'shipping_days' => 3,
            'active' => true,
        ];
    }
}
