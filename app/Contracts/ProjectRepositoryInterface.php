<?php

namespace App\Contracts;

use App\Models\Project;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;

interface ProjectRepositoryInterface
{
    public function getProjectMembersQuery(Project $project): Builder;

    public function getUserProjectsQuery(User $user): Builder;

    public function create(array $attributes): Project;

    public function storeProjectMembers(Project $project, array $member_ids): void;

    public function createBacklogList(Project $project): void;

    public function update(Project $project, array $attributes): bool;

    public function delete(Project $project): bool;

    public function updateProjectMembers(Project $project, array $members): void;

    public function deleteProjectMember(Project $project, User $user): void;
}