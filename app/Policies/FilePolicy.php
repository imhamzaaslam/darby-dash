<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class FilePolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function store(User $user, string $uuid)
    {
        return (auth()->user()->hasRole('customer') && auth()->user()->uuid === $uuid) ||
            (auth()->user()->hasRole('admin'));
    }

    public function canUploadFile(User $user, User $model): bool
    {
        return ($user->hasRole('customer') && $user->id === $model->id) ||
            ($user->hasRole('admin'));
    }
}
