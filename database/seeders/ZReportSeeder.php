<?php

namespace Database\Seeders;

use App\Models\ZReport;
use Illuminate\Database\Seeder;

class ZReportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ZReport::factory()->count(40)->create();
    }
}
