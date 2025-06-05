<?php

namespace Database\Seeders;

use App\Models\Order;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 1; $i <= 21; $i++) {
            Order::factory()->create([
                'status' => 'pagado',
                'payment_method' => 'efectivo',
                'paid_at' => now()->subDays($i),
            ]);
        }
        for ($i = 1; $i <= 21; $i++) {
            Order::factory()->create([
                'status' => 'en preparacion',
                'payment_method' => null,
                'paid_at' => null,
            ]);
        }
        for ($i = 1; $i <= 21; $i++) {
            Order::factory()->create([
                'status' => 'pendiente',
                'payment_method' => null,
                'paid_at' => null,
            ]);
        }
        for ($i = 1; $i <= 21; $i++) {
            Order::factory()->create([
                'status' => 'pagado',
                'payment_method' => 'tarjeta',
                'paid_at' => now()->subDays($i),
            ]);
        }
    }
}
