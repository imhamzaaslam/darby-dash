<?php

namespace App\Contracts;

use App\Models\ProjectList;
use App\Models\Project;
use Illuminate\Support\Collection;

interface ProjectListRepositoryInterface
{
    public function getProjectLists(Project $project): Collection;

    public function create(array $attributes): ProjectList;

    public function update(ProjectList $projectList, array $attributes): bool;

    public function delete(ProjectList $projectList): bool;

    public function getListTasks(ProjectList $projectList): Collection;

    public function getListWithoutMilestone(Project $project): Collection;
}