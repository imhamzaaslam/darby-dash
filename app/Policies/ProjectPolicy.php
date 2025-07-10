<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Project;
use App\Enums\UserRole;
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
        return $user->hasRole(UserRole::ADMIN->value);
    }

    /**
     * Determine whether the user can view all models.
     *
     * @param User $user
     * @return bool
     */
    public function viewAll(User $user): bool
    {
        return
            $user->hasRole(UserRole::ADMIN->value) ||
            $user->hasRole(UserRole::PROJECT_MANAGER->value) ||
            $user->hasRole(UserRole::CLIENT->value) ||
            $user->hasRole(UserRole::STAFF->value) ||
            $user->hasRole(UserRole::DEVELOPER->value) ||
            $user->hasRole(UserRole::DESIGNER->value);
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
        return $user->hasRole(UserRole::ADMIN->value) || $project->members->contains($user);
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return $user->hasRole(UserRole::ADMIN->value) || $user->hasRole(UserRole::PROJECT_MANAGER->value);
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
        return $user->hasRole(UserRole::ADMIN->value) || $user->hasRole(UserRole::PROJECT_MANAGER->value) && $project->members->contains($user);
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
        return $user->hasRole(UserRole::ADMIN->value) || $user->hasRole(UserRole::PROJECT_MANAGER->value) && $project->members->contains($user);
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
        return $user->hasRole(UserRole::ADMIN->value) || $project->members->contains($user);
    }
    
    /**
     * Determine whether the user can update bucks.
     *
     * @param User $user
     * @param Project $project
     * @return bool
     */
    public function updatebucks(User $user, Project $project): bool
    {
        return $user->hasRole(UserRole::ADMIN->value) || $user->hasRole(UserRole::PROJECT_MANAGER->value) && $project->members->contains($user);
    }
}
