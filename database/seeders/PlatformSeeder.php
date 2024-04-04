<?php

namespace Database\Seeders;

use App\Models\Platform;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PlatformSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $bolCom = Platform::create([
            'uuid' => str()->uuid(),
            'name' => 'Bol.com',
            'state' => 'active',
            'client' => 'Bol.com',
        ]);

        $amazon = Platform::create([
            'uuid' => str()->uuid(),
            'name' => 'Amazon',
            'state' => 'inactive',
        ]);

        $shopify = Platform::create([
            'uuid' => str()->uuid(),
            'name' => 'Shopify',
            'state' => 'inactive',
        ]);
    }
}
