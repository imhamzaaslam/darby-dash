<?php

namespace App\Repositories;

use App\Contracts\AbstractEloquentRepository;
use App\Contracts\ProjectPhaseRepositoryInterface;
use App\Models\Base;
use App\Models\ProjectPhase;
use App\Models\Project;
use Illuminate\Support\Collection;

class ProjectPhaseRepository extends AbstractEloquentRepository implements ProjectPhaseRepositoryInterface
{
    /**
     * @var ProjectPhase
     */
    protected Base $model;

    public function __construct(ProjectPhase $model)
    {
        parent::__construct($model);
    }

    public function getProjectPhases(Project $project): Collection
    {
        return $project->phases;
    }

    public function create(array $attributes): ProjectPhase
    {
        return $this->model->create($attributes);
    }

    public function update(ProjectPhase $projectPhase, array $attributes): bool
    {
        return $projectPhase->fill($attributes)->save();
    }

    public function delete(ProjectPhase $projectPhase): bool
    {
        return $projectPhase->delete();
    }

    public function getPhaseTasks(ProjectPhase $projectPhase): Collection
    {
        return $projectPhase->tasks;
    }
}