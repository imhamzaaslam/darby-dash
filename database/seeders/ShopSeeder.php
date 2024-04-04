<?php

namespace Database\Seeders;

use App\Models\Platform;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Database\Seeder;

class ShopSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $platform = Platform::whereClient('Bol.com')->first();
        $users = User::all();

        Shop::factory()->count($users->count())
            ->recycle($users)
            ->recycle($platform)
            ->create();
    }
}
