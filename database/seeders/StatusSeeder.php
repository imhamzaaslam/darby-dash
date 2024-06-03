<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Status;
use Illuminate\Support\Facades\DB;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::beginTransaction();
        try {
            $statuses = [
                ['name' => 'To Do', 'color' => 'secondary', 'display_order' => 0],
                ['name' => 'In Progress', 'color' => 'primary', 'display_order' => 1],
                ['name' => 'Completed', 'color' => 'success', 'display_order' => 2],
            ];

            foreach ($statuses as $status) {
                Status::create($status);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
