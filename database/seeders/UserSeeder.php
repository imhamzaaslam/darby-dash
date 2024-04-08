<?php

namespace Database\Seeders;

use App\Models\User;
use Faker\Generator;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @param Generator $faker
     * @return void
     */
    public function run(Generator $faker): void
    {
         $demoUser = User::create([
             'uuid'              => $faker->uuid,
             'name_first'        => $faker->firstName,
             'name_last'         => $faker->lastName,
             'email'             => 'demo@demo.com',
             'password'          => Hash::make('password'),
             'email_verified_at' => now(),
             'state'             => 'active',
         ]);

         $demoUser2 = User::create([
             'uuid'              => $faker->uuid,
             'name_first'        => $faker->firstName,
             'name_last'         => $faker->lastName,
             'email'             => 'admin@demo.com',
             'password'          => Hash::make('password'),
             'email_verified_at' => now(),
             'state'             => 'active',
         ]);
    }
}
