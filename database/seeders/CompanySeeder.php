<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Company;
use App\Models\Role;
use App\Enums\UserRole;
use Faker\Generator;
use Illuminate\Support\Facades\Hash;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @param Generator $faker
     * @return void
     */
    public function run(Generator $faker): void
    {
        $company = Company::firstOrCreate([
            'name' => 'SublimeLogics',
        ]);

        $superadmin = User::firstOrCreate([
            'name_first' => 'Hamza',
            'name_last' => 'Aslam',
            'email' => 'hamza@superadmin.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'state' => 'active',
        ]);

        $this->addDummyInfo($faker, $superadmin);

        $role = Role::where('name', UserRole::SUPER_ADMIN)->first();

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
    private function addDummyInfo(Generator $faker, User $user): void
    {
        $user->info()->create([
            'phone' => $faker->phoneNumber,
        ]);
    }
}
