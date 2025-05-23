<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProjectTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::beginTransaction();
        try {
            $projectTypes = [
                [
                    'name' => 'Web Development',
                    'icon' => 'tabler-world',
                    'display_order' => 1,
                    'uuid' => Str::uuid(),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'name' => 'SEO Program',
                    'icon' => 'tabler-military-rank',
                    'display_order' => 2,
                    'uuid' => Str::uuid(),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'name' => 'Google Ads Program',
                    'icon' => 'tabler-brand-google',
                    'display_order' => 3,
                    'uuid' => Str::uuid(),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
            ];
    
            foreach ($projectTypes as $projectType) {
                if (!DB::table('project_types')->where('name', $projectType['name'])->exists()) {
                    DB::table('project_types')->insert($projectType);
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
