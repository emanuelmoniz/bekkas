<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Address;
use App\Models\Country;
use App\Models\User;

class AddressFactory extends Factory
{
    protected $model = Address::class;

    public function definition()
    {
        return [
            'title' => 'Home',
            'nif' => null,
            'phone' => '000000000',
            'address_line_1' => $this->faker->streetAddress,
            'address_line_2' => null,
            'postal_code' => $this->faker->postcode,
            'city' => $this->faker->city,
            'country_id' => Country::factory(),
            'is_default' => false,
            'user_id' => User::factory(),
        ];
    }
}
