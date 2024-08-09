<?php

namespace App\Contracts;

use Illuminate\Support\Collection;
use App\Models\Role;

interface RoleRepositoryInterface
{
    public function get(): Collection;

    public function getPermissions(Role $role): array;

    public function createPermissions(Role $role, array $permissions): void;
}