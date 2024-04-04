<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $data = $this->data();

        foreach ($data as $value) {
            Role::create([
                'name' => $value['name'],
            ]);
        }

        $admin = User::where('email', 'admin@demo.com')->first();
        $admin?->assignRole(Role::where('name', 'admin')->first());

        $customer = User::where('email', 'demo@demo.com')->first();
        $customer?->assignRole(Role::where('name', 'customer')->first());

        User::all()
            ->reject(fn (User $user) => in_array($user->email, ['admin@demo.com', 'demo@demo.com'], true))
            ->each(fn (User $user) => $user->assignRole('customer'));
    }

    public function data(): array
    {
        return [
            ['name' => 'admin'],
            ['name' => 'customer'],
        ];
    }
}
