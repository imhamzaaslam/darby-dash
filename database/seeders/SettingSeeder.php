<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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
                ['name' => 'General'],
                ['name' => 'Notification'],
                ['name' => 'Email'],
            ];

            foreach ($settings as $setting) {
                $exists = DB::table('settings')->where('name', $setting['name'])->exists();
                if (!$exists) {
                    DB::table('settings')->insert([
                        'name' => $setting['name'],
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
