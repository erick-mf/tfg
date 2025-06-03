<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MenuCategory>
 */
class MenuCategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->randomElement([
                'Platos',
                'Bebidas',
                'Entrantes',
                'Postres',
                'Sin gluten',
                'Sopas y cremas',
                'Vegano',
                'Infantil',
                'Especialidades',
                'Cafe',
            ]),
        ];
    }
}
