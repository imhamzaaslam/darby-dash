<?php

namespace App\Repositories;

use App\Contracts\AbstractEloquentRepository;
use App\Contracts\ProjectListRepositoryInterface;
use App\Models\Base;
use App\Models\ProjectList;
use App\Models\Project;
use Illuminate\Support\Collection;

class ProjectListRepository extends AbstractEloquentRepository implements ProjectListRepositoryInterface
{
    /**
     * @var ProjectList
     */
    protected Base $model;

    public function __construct(ProjectList $model)
    {
        parent::__construct($model);
    }

    public function getProjectLists(Project $project): Collection
    {
        return $project->lists;
    }

    public function create(array $attributes): ProjectList
    {
        return $this->model->create($attributes);
    }

    public function update(ProjectList $projectList, array $attributes): bool
    {
        return $projectList->fill($attributes)->save();
    }

    public function delete(ProjectList $projectList): bool
    {
        return $projectList->delete();
    }

    public function getListTasks(ProjectList $projectList): Collection
    {
        return $projectList->tasks;
    }
}
