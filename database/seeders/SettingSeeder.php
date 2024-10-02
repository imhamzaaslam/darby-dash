<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;

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
                if(!Setting::where('name', $setting['name'])->exists()) {
                    Setting::create($setting);
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
