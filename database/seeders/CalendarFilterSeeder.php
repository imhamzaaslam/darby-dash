<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Str;

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
                'uuid' => Str::uuid(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Business',
                'color' => 'primary',
                'uuid' => Str::uuid(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Family',
                'color' => 'warning',
                'uuid' => Str::uuid(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Holiday',
                'color' => 'success',
                'uuid' => Str::uuid(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'ETC',
                'color' => 'info',
                'uuid' => Str::uuid(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        foreach ($data as $item) {
            if (!DB::table('calendar_filters')->where('name', $item['name'])->exists()) {
                DB::table('calendar_filters')->insert($item);
            }
        }
    }
}
