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
                ['name' => 'PENDING', 'color' => 'secondary', 'display_order' => 0],
                ['name' => 'IN PROGRESS', 'color' => 'primary', 'display_order' => 1],
                ['name' => 'COMPLETED', 'color' => 'success', 'display_order' => 2],
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
