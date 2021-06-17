<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (config('app.env') !== 'production') {
            $this->call([
                CompanySeeder::class,
                UserSeeder::class,
                EmployeeSeeder::class,
                EmployeeRequestSeeder::class,
            ]);
        }
    }
}
