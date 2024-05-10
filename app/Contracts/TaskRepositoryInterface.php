<?php

namespace App\Contracts;

use App\Models\Task;
use App\Models\Project;
use Illuminate\Support\Collection;

interface TaskRepositoryInterface
{
    public function get(string $projecId): Collection;
    
    public function create(Project $project, array $attributes): Task;

    public function update(Task $task, array $attributes): bool;

    public function delete(Task $task): bool;
}