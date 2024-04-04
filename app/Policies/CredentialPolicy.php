<?php

namespace App\Policies;

use App\Models\Credential;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class CredentialPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param User $auth
     * @param Shop $shop
     * @param User $user
     * @return Response|bool
     */
    public function viewAny(User $auth, Shop $shop, User $user): Response|bool
    {
        return $auth->is($shop->user) || $auth->hasRole('admin') && $shop->user->is($user);
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param Credential $credential
     * @return Response|bool
     */
    public function view(User $user, Credential $credential): Response|bool
    {
        return (auth()->user()->is($user) && $credential->shop->user->is($user)) ||
            (auth()->user()->hasRole('admin'));
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $auth
     * @param Shop $shop
     * @param User|null $user
     * @return Response|bool
     */
    public function create(User $auth, Shop $shop, User $user = null): Response|bool
    {
        return $auth->is($shop->user) || $auth->hasRole('admin') && $shop->user->is($user);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param Credential $credential
     * @return Response|bool
     */
    public function update(User $user, Credential $credential): Response|bool
    {
        return ($credential->shop->user->is($user) && $user->hasRole('customer')) || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param Credential $credential
     * @return Response|bool
     */
    public function delete(User $user, Credential $credential): Response|bool
    {
        return ($credential->shop->user->is($user) && $user->hasRole('customer')) || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param User $user
     * @param Credential $credential
     * @return Response|bool
     */
    public function restore(User $user, Credential $credential)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param User $user
     * @param Credential $credential
     * @return Response|bool
     */
    public function forceDelete(User $user, Credential $credential)
    {
        //
    }
}
