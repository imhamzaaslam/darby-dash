<?php

namespace App\Contracts;

use App\Models\User;
use Illuminate\Support\Collection;

interface UserRepositoryInterface
{
    public function create(string $role, array $attributes, array $infoAttributes): User;

    public function update(User $user, array $attributes): bool;

    public function delete(User $user): bool;

    public function activate(User $user): bool;

    public function deactivate(User $user): bool;

    public function getActiveCustomers(): Collection;

    public function updateMeta(User $user, array $metaAttributes): bool;

    public function unsetMeta(User $user, string $key): bool;

    public function getAdmins(): Collection;

    public function getByRole(string $role): Collection;
}
