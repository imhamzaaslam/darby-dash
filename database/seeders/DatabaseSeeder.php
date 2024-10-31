<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
            UserSeeder::class,
            ProjectTypeSeeder::class,
            ProjectSeeder::class,
            CalendarFilterSeeder::class,
            StatusSeeder::class,
            SettingSeeder::class,
            ProjectServiceSeeder::class,
            CompanySeeder::class,
            SuperAdminSeeder::class,
        ]);
    }
}
