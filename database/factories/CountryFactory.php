<?php

namespace Database\Factories;

use App\Models\Country;
use App\Models\CountryTranslation;
use Illuminate\Database\Eloquent\Factories\Factory;

class CountryFactory extends Factory
{
    protected $model = Country::class;

    public function definition()
    {
        return [
            'iso_alpha2'   => 'PT',
            'country_code' => '+351',
            'is_active'    => true,
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Country $country) {
            foreach (array_keys(config('app.locales', ['pt-PT' => 'Português', 'en-UK' => 'English'])) as $locale) {
                CountryTranslation::firstOrCreate(
                    ['country_id' => $country->id, 'locale' => $locale],
                    ['name' => 'Portugal']
                );
            }
        });
    }
}
