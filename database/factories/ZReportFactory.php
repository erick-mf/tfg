<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ZReport>
 */
class ZReportFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => fake()->randomElement(User::all())->id,
            'start_date' => fake()->date('d-m-Y H:i'),
            'end_date' => fake()->date('d-m-Y H:i'),
            'total_revenue' => fake()->randomFloat(2, 0, 1000),
            'expected_cash' => fake()->randomFloat(2, 0, 1000),
            'counted_cash' => fake()->randomFloat(2, 0, 1000),
            'cash_difference' => fake()->randomFloat(2, 0, 1000),
            'total_card' => fake()->randomFloat(2, 0, 1000),
            'total_orders' => fake()->randomFloat(2, 0, 1000),
            'notes' => fake()->text(),
        ];
    }
}
