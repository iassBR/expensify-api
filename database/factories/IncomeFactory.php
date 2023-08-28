<?php

namespace Database\Factories;

use App\Models\Income;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Income>
 */
class IncomeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'description' => fake()->randomElement(['Salário', 'Freela', 'Pai']),
            'value' => fake()->randomFloat(1, 2000, 10000),
            'type' => fake()->randomElement(Income::TYPES),
        ];
    }
}
