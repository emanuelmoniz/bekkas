<?php

namespace Database\Factories;

use App\Models\Tax;
use App\Models\TaxTranslation;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaxFactory extends Factory
{
    protected $model = Tax::class;

    public function definition()
    {
        return [
            'percentage' => 23,
            'is_active' => true,
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Tax $tax) {
            foreach (array_keys(config('app.locales', ['pt-PT' => 'Português', 'en-UK' => 'English'])) as $locale) {
                TaxTranslation::firstOrCreate(
                    ['tax_id' => $tax->id, 'locale' => $locale],
                    ['name' => 'VAT']
                );
            }
        });
    }
}
