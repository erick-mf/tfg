<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => env('ADMIN_NAME'),
            'surnames' => env('ADMIN_SURNAMES'),
            'password' => env('ADMIN_PASS'),
            'phone' => env('ADMIN_PHONE'),
            'role' => env('ADMIN_ROLE'),
        ]);
    }
}
