<?php

namespace App\Contracts;

use App\Models\Project;
use App\Models\ProjectBucks;
use App\Models\Task;
use Illuminate\Support\Collection;

interface ProjectBucksRepositoryInterface
{
    public function index(Project $project): Collection;

    public function update(Project $project, array $data): ProjectBucks;
    
    public function updateTaskApprovalStatus(Task $task, array $attributes): bool;
    
    public function getRoleShare(Project $project, int $roleId): string;
}
