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

        for ($i = 2; $i < 31; $i++) {
            User::factory()->create([
                'name' => fake()->name(),
                'role' => fake()->randomElement(['cocinero', 'camarero']),
                'password' => '12345'.$i,
            ]);
        }
    }
}
