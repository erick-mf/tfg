<?php

namespace Database\Factories;

use App\Models\MenuItem;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrderItem>
 */
class OrderItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $quantity = fake()->numberBetween(1, 99);
        $menuItem = MenuItem::inRandomOrder()->first();
        $order = Order::inRandomOrder()->first();
        $subtotal = round($menuItem->price * $quantity, 2);

        return [
            'quantity' => $quantity,
            'unit_price' => $menuItem->price,
            'subtotal' => $subtotal,
            'status' => fake()->randomElement(['enviado', 'cancelado', 'en preparacion']),
            'notes' => fake()->sentence(5),
            'order_id' => $order->id,
            'menu_item_id' => $menuItem->id,
        ];
    }
}
