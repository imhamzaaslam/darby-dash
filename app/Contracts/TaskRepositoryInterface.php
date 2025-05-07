<?php

namespace App\Contracts;

use App\Models\Task;
use App\Models\ProjectList;
use App\Models\Project;
use Illuminate\Support\Collection;

interface TaskRepositoryInterface
{
    public function get(string $projecId): Collection;

    public function create(ProjectList $list, array $attributes): Task;

    public function update(Task $task, array $attributes): bool;

    public function delete(Task $task): bool;

    public function fetchUnlistedTasks(Project $project): Collection;

    public function getByProject(Project $project): Collection;

    public function getDueTasksByProject(Project $project): Collection;

    public function createByProject(Project $project, array $attributes): Task;

    public function updateTasksOrder(Task $task, array $attributes): Task;

    public function getMembersForTask(Project $project, Task $task, string $keyword = null): Collection;

    public function assginMember(Task $task, array $attributes): bool;
    
    public function fetchBucksTasks(Project $project): Collection;
}

