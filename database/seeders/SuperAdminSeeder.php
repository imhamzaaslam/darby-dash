<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use App\Enums\UserRole;
use Faker\Generator;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @param Generator $faker
     * @return void
     */
    public function run(Generator $faker): void
    {
        $user = [
            'name_first' => 'Eric',
            'name_last' => 'Wing',
            'email' => 'super@admin.com',
            'role' => UserRole::SUPER_ADMIN,
        ];

        $superadmin = User::create([
            'uuid' => $faker->uuid,
            'name_first' => $user['name_first'],
            'name_last' => $user['name_last'],
            'email' => $user['email'],
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'state' => 'active',
        ]);

        $this->addDummyInfo($faker, $superadmin);

        $role = Role::where('name', $user['role'])->first();
        if ($role) {
            $superadmin->assignRole($role);
        }
    }

    /**
     * Add dummy user info.
     *
     * @param Generator $faker
     * @param User $user
     */
    private function addDummyInfo(Generator $faker, User $superadmin): void
    {
        $superadmin->info()->create([
            'phone' => $faker->phoneNumber,
        ]);
    }
}
