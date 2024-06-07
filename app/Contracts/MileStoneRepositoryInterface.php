<?php

namespace App\Contracts;

use App\Models\MileStone;
use App\Models\Project;
use Illuminate\Support\Collection;

interface MileStoneRepositoryInterface
{
    public function create(Project $project, array $attributes): MileStone;

    public function syncProjectLists(MileStone $mileStone, array $projectListIds): void;

    public function update(MileStone $mileStone, array $attributes): bool;

    public function delete(MileStone $mileStone): bool;
}