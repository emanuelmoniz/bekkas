<?php

namespace Database\Factories;

use App\Models\Address;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

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
            'country_id' => function () {
                $country = \App\Models\Country::firstOrCreate(
                    ['iso_alpha2' => 'PT'],
                    ['country_code' => '+351', 'is_active' => true]
                );

                foreach (array_keys(config('app.locales', ['pt-PT' => 'Português', 'en-UK' => 'English'])) as $locale) {
                    \App\Models\CountryTranslation::firstOrCreate(
                        ['country_id' => $country->id, 'locale' => $locale],
                        ['name' => 'Portugal']
                    );
                }

                return $country->id;
            },
            'is_default' => false,
            'user_id' => User::factory(),
        ];
    }
}
