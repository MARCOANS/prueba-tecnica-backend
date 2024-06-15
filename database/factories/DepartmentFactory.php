<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Department>
 */
class DepartmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->company(),
            'level' => $this->faker->numberBetween(1, 10),
            'employees' => $this->faker->numberBetween(1, 100),
            'ambassador' => rand(0, 1) ? $this->faker->name : null,
        ];
    }
}
