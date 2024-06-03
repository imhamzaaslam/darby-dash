<?php

namespace App\Contracts;

use App\Models\Project;
use Illuminate\Support\Collection;

interface ProjectRepositoryInterface
{
    public function create(array $attributes): Project;

    public function storeProjectMembers(Project $project, array $members): void;

    public function createBacklogList(Project $project): void;

    public function update(Project $project, array $attributes): bool;

    public function delete(Project $project): bool;
}