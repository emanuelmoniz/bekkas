<?php

namespace Database\Factories;

use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectFactory extends Factory
{
    protected $model = Project::class;

    public function definition()
    {
        return [
            'production_date' => $this->faker->date(),
            'execution_time' => $this->faker->randomFloat(2, 1, 10),
            'dimensions' => $this->faker->word,
            'weight' => $this->faker->randomFloat(2, 0.1, 5),
            'is_active' => true,
            'is_featured' => false,
        ];
    }
}
