<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Platform;
use App\Models\User;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $platform = Platform::whereClient('Bol.com')->first();
        $categories = Category::all();

        Product::factory()->count(300)
            ->recycle($platform)
            ->recycle($categories)
            ->create();
    }
}
