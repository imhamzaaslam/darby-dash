<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CalendarFilter;

class CalendarFilterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'name' => 'Personal',
                'color' => 'error',
            ],
            [
                'name' => 'Business',
                'color' => 'primary',
            ],
            [
                'name' => 'Family',
                'color' => 'warning',
            ],
            [
                'name' => 'Holiday',
                'color' => 'success',
            ],
            [
                'name' => 'ETC',
                'color' => 'info',
            ],
        ];

        foreach ($data as $item) {
            CalendarFilter::create($item);
        }
    }
}
