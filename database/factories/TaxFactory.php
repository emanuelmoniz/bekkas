<?php

namespace Database\Factories;

use App\Models\Tax;
use Illuminate\Database\Eloquent\Factories\Factory;

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
