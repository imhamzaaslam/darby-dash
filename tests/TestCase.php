<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Spatie\Permission\Models\Role;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function user(string $role = 'admin', array $attributes = []): User
    {
        $user = User::factory()->create($attributes);
        $role = Role::firstOrCreate(['name' => $role]);

        $user->assignRole($role);

        return $user;
    }
}
