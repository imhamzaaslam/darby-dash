<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Project;
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
        return $user->hasRole('Super Admin');
    }

    /**
     * Determine whether the user can view all models.
     *
     * @param User $user
     * @return bool
     */
    public function viewAll(User $user): bool
    {
        return $user->hasRole('Super Admin') || $user->hasRole('Project Manager') || $user->hasRole('Client User') || $user->hasRole('Staff User');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param Project $project
     * @return bool
     */
    public function view(User $user, Project $project): bool
    {
        return $user->hasRole('Super Admin') || $project->members->contains($user);
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return $user->hasRole('Super Admin') || $user->hasRole('Project Manager');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param Project $project
     * @return bool
     */
    public function update(User $user, Project $project): bool
    {
        return $user->hasRole('Super Admin') || $user->hasRole('Project Manager') && $project->members->contains($user);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param Project $project
     * @return bool
     */
    public function delete(User $user, Project $project): bool
    {
        return $user->hasRole('Super Admin') || $user->hasRole('Project Manager') && $project->members->contains($user);
    }

    /**
     * Determine whether the user can view bucks.
     *
     * @param User $user
     * @param Project $project
     * @return bool
     */
    public function viewbucks(User $user, Project $project): bool
    {
        return $user->hasRole('Super Admin') || $user->hasRole('Project Manager') && $project->members->contains($user);
    }
}
