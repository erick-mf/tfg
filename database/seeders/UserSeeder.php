<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'admin',
            'role' => 'admin',
            'password' => '12345a',
        ]);

        User::factory()->create([
            'name' => 'encargado',
            'role' => 'encargado',
            'password' => '12345b',
        ]);

        for ($i = 1; $i < 6; $i++) {
            User::factory()->create([
                'name' => fake()->name(),
                'role' => fake()->randomElement(['camarero']),
                'password' => '1234'.$i.'c',
            ]);
        }

        for ($i = 1; $i < 6; $i++) {
            User::factory()->create([
                'name' => fake()->name(),
                'role' => fake()->randomElement(['cocinero']),
                'password' => '1234'.$i.'d',
            ]);
        }
    }
}
