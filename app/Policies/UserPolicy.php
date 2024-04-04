<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param User $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        return auth()->user() === $user && $user->hasRole('admin');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param User $model
     * @return bool
     */
    public function view(User $user, User $model): bool
    {
        return (auth()->user() === $user && $user->hasRole('customer')) ||
            (auth()->user()->hasRole('admin'));
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return auth()->user() === $user && $user->hasRole('admin');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @return bool
     */
    public function update(User $user): bool
    {
        return auth()->user() === $user;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param User $model
     * @return bool
     */
    public function delete(User $user, User $model): bool
    {
        return $user->is($model) || auth()->user()->hasRole('admin');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param User $user
     * @param User $model
     * @return mixed
     */
    public function restore(User $user, User $model)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param User $user
     * @param User $model
     * @return mixed
     */
    public function forceDelete(User $user, User $model)
    {
        //
    }

    /**
     * Determine whether the user can update oss.
     *
     * @param User $user
     * @return Response|bool
     */
    public function updateOss(User $user): Response|bool
    {
        return  ( auth()->user() === $user ||
        auth()->user()->hasRole('admin'));
    }

    public function canSeeStatistics(User $user): bool
    {
        return auth()->user() === $user && auth()->user()->hasRole('customer');
    }


    public function canSeeVatStatistics(User $user): bool
    {
        return auth()->user() === $user && auth()->user()->hasRole('customer');
    }

    public function canUploadFile(User $user, User $model): bool
    {
        return ($user->hasRole('customer') && $user->id === $model->id) || ($user->hasRole('admin'));
    }
}
