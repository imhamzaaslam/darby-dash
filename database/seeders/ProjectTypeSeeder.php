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
                ['name' => 'Website Design Project', 'icon' => 'tabler-world', 'display_order' => 1],
                ['name' => 'SEO Program', 'icon' => 'tabler-military-rank', 'display_order' => 2],
                ['name' => 'Google Ads Program', 'icon' => 'tabler-brand-google', 'display_order' => 3],
            ];
    
            foreach ($projectTypes as $projectType) {
                if(!ProjectType::where('name', $projectType['name'])->exists()) {
                    ProjectType::create($projectType);
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
