<?php

namespace App\Contracts;

use App\Models\MileStone;
use App\Models\Project;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;

interface MileStoneRepositoryInterface
{
    public function getByProjectQuery(Project $project): Builder;

    public function create(Project $project, array $attributes): MileStone;

    public function syncProjectLists(MileStone $mileStone, array $projectListIds): void;

    public function update(MileStone $mileStone, array $attributes): bool;

    public function delete(MileStone $mileStone): bool;
}