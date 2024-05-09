<?php

namespace App\Repositories;

use App\Contracts\AbstractEloquentRepository;
use App\Contracts\ProjectRepositoryInterface;
use App\Models\Base;
use App\Models\Project;
use App\Models\ProjectMember;

class ProjectRepository extends AbstractEloquentRepository implements ProjectRepositoryInterface
{
    /**
     * @var Project
     */
    protected Base $model;

    public function __construct(Project $model)
    {
        parent::__construct($model);
    }

    public function create(array $attributes): Project
    {
        return $this->model->create($attributes);
    }

    public function storeProjectMembers(Project $project, array $members): void
    {
        $project->members()->sync($members);
    }

    public function updateProjectMembers(Project $project, array $members): void
    {
        ProjectMember::where('project_id', $project->id)->delete();
        $this->storeProjectMembers($project, $members);
    }

    public function update(Project $project, array $attributes): bool
    {
        return $project->fill($attributes)->save();
    }

    public function delete(Project $project): bool
    {
        return $project->delete();
    }
}