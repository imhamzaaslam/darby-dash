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
            'name_first'        => 'Eric',
            'name_last'         => 'Wing',
            'email'             => 'eric@darby.com',
            'password'          => Hash::make('password'),
            'email_verified_at' => now(),
            'state'             => 'active',
        ]);

        $this->addDummyInfo($faker, $demoUser);
        $demoUser->assignRole(Role::where('name', 'project manager')->first());

        $demoUser2 = User::create([
            'uuid'              => $faker->uuid,
            'name_first'        => 'Hamza',
            'name_last'         => 'Aslam',
            'email'             => 'hamza@gmail.com',
            'password'          => Hash::make('password'),
            'email_verified_at' => now(),
            'state'             => 'active',
        ]);

        $this->addDummyInfo($faker, $demoUser2);
        $demoUser2->assignRole(Role::where('name', 'admin')->first());

        $demoUser3 = User::create([
            'uuid'              => $faker->uuid,
            'name_first'        => 'Noman',
            'name_last'         => 'Javeed',
            'email'             => 'noman@gmail.com',
            'password'          => Hash::make('password'),
            'email_verified_at' => now(),
            'state'             => 'active',
        ]);

        $this->addDummyInfo($faker, $demoUser3);
        $demoUser3->assignRole(Role::where('name', 'viewer')->first());

        $demoUser4 = User::create([
            'uuid'              => $faker->uuid,
            'name_first'        => 'Muzammil',
            'name_last'         => 'Shahzad',
            'email'             => 'muzammil@gmail.com',
            'password'          => Hash::make('password'),
            'email_verified_at' => now(),
            'state'             => 'active',
        ]);

        $this->addDummyInfo($faker, $demoUser4);
        $demoUser4->assignRole(Role::where('name', 'developer')->first());

        $demoUser5 = User::create([
            'uuid'              => $faker->uuid,
            'name_first'        => 'Umer',
            'name_last'         => 'Khan',
            'email'             => 'developer',
            'password'          => Hash::make('password'),
            'email_verified_at' => now(),
            'state'             => 'active',
        ]);

        $this->addDummyInfo($faker, $demoUser5);
        $demoUser5->assignRole(Role::where('name', 'developer')->first());

        $demoUser6 = User::create([
            'uuid'              => $faker->uuid,
            'name_first'        => 'Awais',
            'name_last'         => 'Ali',
            'email'             => 'awais@gmail.com',
            'password'          => Hash::make('password'),
            'email_verified_at' => now(),
            'state'             => 'active',
        ]);

        $this->addDummyInfo($faker, $demoUser6);
        $demoUser6->assignRole(Role::where('name', 'developer')->first());

        // User::factory(20)->create()->each(function ($user) use ($faker) {
        //     $this->addDummyInfo($faker, $user);
        //     $role = $user->id % 2 === 0 ? Role::where('name', 'project manager')->first() : Role::where('name', 'developer')->first();
        //     $user->assignRole($role);
        // });
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
