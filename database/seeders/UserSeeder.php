<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
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

        $this->addDummyInfo($faker, $demoUser);
        $demoUser->assignRole(Role::where('name', 'viewer')->first());

        $demoUser2 = User::create([
            'uuid'              => $faker->uuid,
            'name_first'        => $faker->firstName,
            'name_last'         => $faker->lastName,
            'email'             => 'admin@demo.com',
            'password'          => Hash::make('password'),
            'email_verified_at' => now(),
            'state'             => 'active',
        ]);

        $this->addDummyInfo($faker, $demoUser2);
        $demoUser2->assignRole(Role::where('name', 'admin')->first());

        User::factory(20)->create()->each(function ($user) use ($faker) {
            $this->addDummyInfo($faker, $user);
            $role = $user->id % 2 === 0 ? Role::where('name', 'pm')->first() : Role::where('name', 'developer')->first();
            $user->assignRole($role);
        });
    }

    /**
     * Add dummy user info.
     *
     * @param Generator $faker
     * @param User $user
     */
    private function addDummyInfo(Generator $faker, User $user): void
    {
        $user->info()->create([
            'phone'   => $faker->phoneNumber,
        ]);
    }
}
