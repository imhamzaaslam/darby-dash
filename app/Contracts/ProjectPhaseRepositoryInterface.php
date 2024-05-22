<?php

namespace App\Contracts;

use App\Models\ProjectPhase;
use App\Models\Project;
use Illuminate\Support\Collection;

interface ProjectPhaseRepositoryInterface
{
    public function getProjectPhases(Project $project): Collection;

    public function create(array $attributes): ProjectPhase;

    public function update(ProjectPhase $projectPhase, array $attributes): bool;

    public function delete(ProjectPhase $projectPhase): bool;

    public function getPhaseTasks(ProjectPhase $projectPhase): Collection;
}