<?php

namespace Database\Factories;

use App\Models\Country;
use Illuminate\Database\Eloquent\Factories\Factory;

class CountryFactory extends Factory
{
    protected $model = Country::class;

    public function definition()
    {
        return [
            'name_pt' => 'Portugal',
            'name_en' => 'Portugal',
            'iso_alpha2' => 'PT',
            'country_code' => '351',
            'is_active' => true,
        ];
    }
}
