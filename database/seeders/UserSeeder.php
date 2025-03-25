<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Company;
use App\Models\Role;
use App\Enums\UserRole;
use Faker\Generator;
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
        $company = Company::firstOrCreate(
            ['id' => 1],
            ['name' => 'WithDarby']
        );
        $users = [
            [
                'name_first' => 'Eric',
                'name_last' => 'Wing',
                'email' => 'super@admin.com',
                'role' => UserRole::SUPER_ADMIN,
            ],
            [
                'name_first' => 'Eric',
                'name_last' => 'Wing',
                'email' => 'eric@darby.com',
                'role' => UserRole::ADMIN,
            ],
            [
                'name_first' => 'Hamza',
                'name_last' => 'Aslam',
                'email' => 'hamza@gmail.com',
                'role' => UserRole::PROJECT_MANAGER,
            ],
            [
                'name_first' => 'Noman',
                'name_last' => 'Javeed',
                'email' => 'noman@gmail.com',
                'role' => UserRole::CLIENT,
            ],
            [
                'name_first' => 'Muzammil',
                'name_last' => 'Shahzad',
                'email' => 'muzammil@gmail.com',
                'role' => UserRole::STAFF,
            ],
            [
                'name_first' => 'Umer',
                'name_last' => 'Shehzad',
                'email' => 'umer@gmail.com',
                'role' => UserRole::STAFF,
            ],
            [
                'name_first' => 'Awais',
                'name_last' => 'Ali',
                'email' => 'awais@gmail.com',
                'role' => UserRole::STAFF,
            ],
        ];

        foreach ($users as $userData) {
            $user = User::create([
                'uuid' => $faker->uuid,
                'name_first' => $userData['name_first'],
                'name_last' => $userData['name_last'],
                'email' => $userData['email'],
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'state' => 'active',
                'company_id' => $company->id,
            ]);

            $this->addDummyInfo($faker, $user);

            $role = Role::where('name', $userData['role'])->first();
            if ($role) {
                $user->assignRole($role);
            }
        }
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
            'phone' => $faker->phoneNumber,
        ]);
    }
}
