<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ProjectType;
use Illuminate\Support\Facades\DB;

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
                'Website Design Project',
                'SEO Program',
                'Google Ads Program'
            ];
    
            foreach ($projectTypes as $projectType) {
                if (!ProjectType::where('name', $projectType)->exists()) {
                    ProjectType::create(['name' => $projectType]);
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
