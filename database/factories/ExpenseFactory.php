<?php

namespace Database\Factories;

use App\Models\Expense;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Expense>
 */
class ExpenseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'value' => fake()->randomFloat(1, 20, 300),
            'type' => fake()->randomElement(Expense::TYPES),
            'user_id' => User::where('email', 'igor.alexander.silva@gmail.com')->first()
        ];
    }
}
