<?php

namespace Database\Factories;

use App\Models\Table;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'total' => fake()->randomNumber(3, false),
            'status' => fake()->randomElement(['pendiente', 'en preparacion', 'pagado']),
            'payment_method' => fake()->randomElement(['efectivo', 'tarjeta']),
            'paid_at' => fake()->dateTimeBetween('-1 year', 'now'),
            'user_id' => fake()->randomElement(User::all())->id,
            'table_id' => fake()->randomElement(Table::all())->id,
        ];
    }
}
