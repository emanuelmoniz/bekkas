<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Tax;

class TaxFactory extends Factory
{
    protected $model = Tax::class;

    public function definition()
    {
        return [
            'name' => 'VAT',
            'percentage' => 23,
            'is_active' => true,
        ];
    }
}
