<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if ($_ENV['APP_ENV'] == 'local') {
            $this->call([
                UserSeeder::class,
                LocationSeeder::class,
                MenuCategorySeeder::class,
                ProductSeeder::class,
                MenuItemSeeder::class,
                TableSeeder::class,
                OrderSeeder::class,
                OrderItemSeeder::class,
                ZReportSeeder::class,
            ]);
        } else {
            if (! User::where('password', env('ADMIN_PASS'))->exists()) {
                $this->call(AdminSeeder::class);
            }

        }
    }
}
