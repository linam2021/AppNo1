<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\EmployeeSeeder;
use Database\Seeders\SectionSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            SectionSeeder::class,
            EmployeeSeeder::class,
        ]);
    }
}
