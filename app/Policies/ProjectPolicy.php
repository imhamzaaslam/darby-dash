<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Product;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProjectPolicy
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
        return $user->hasRole('admin') || $user->hasRole('customer');
    }
    
    /**
     * Determine whether the user can view all models.
     *
     * @param User $user
     * @return bool
     */
    public function viewAll(User $user): bool
    {
        return $user->hasRole('Super Admin') || $user->hasRole('Project Manager');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param Product $product
     * @return bool
     */
    public function view(User $user, Product $product): bool
    {
        return $user->hasRole('admin') || $user->products->contains($product);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param Product $product
     * @return bool
     */
    public function update(User $user, Product $product): bool
    {
        return $user->hasRole('admin') || $user->products->contains($product);
    }
}
