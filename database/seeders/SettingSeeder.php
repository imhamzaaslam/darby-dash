<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Str;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::beginTransaction();
        try {
            $settings = [
                ['uuid' => Str::uuid(), 'name' => 'General'],
                ['uuid' => Str::uuid(), 'name' => 'Notification'],
                ['uuid' => Str::uuid(), 'name' => 'Email'],
            ];

            foreach ($settings as $setting) {
                $exists = DB::table('settings')->where('name', $setting['name'])->exists();
                if (!$exists) {
                    DB::table('settings')->insert([
                        'name' => $setting['name'],
                        'uuid' => $setting['uuid'],
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]);
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
