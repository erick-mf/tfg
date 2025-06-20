<?php

namespace Database\Factories;

use App\Models\MenuCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MenuItem>
 */
class MenuItemFactory extends Factory
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
            'price' => fake()->randomFloat(2, 1, 100),
            'is_available' => fake()->boolean(),
            'image_path' => 'default.webp',
            'location' => fake()->randomElement(['cocina', 'barra']),
            'menu_category_id' => fake()->randomElement(MenuCategory::all())->id,
        ];
    }
}
