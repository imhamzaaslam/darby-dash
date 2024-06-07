<?php

namespace App\Repositories;

use App\Contracts\AbstractEloquentRepository;
use App\Contracts\MileStoneRepositoryInterface;
use App\Models\Base;
use App\Models\MileStone;
use App\Models\Project;
use App\Models\ProjectList;

class MileStoneRepository extends AbstractEloquentRepository implements MileStoneRepositoryInterface
{
    /**
     * @var MileStone
     */
    protected Base $model;

    public function __construct(MileStone $model)
    {
        parent::__construct($model);
    }

    public function create(Project $project, array $attributes): MileStone
    {
        return $this->model->create(array_merge($attributes, ['project_id' => $project->id]));
    }

    public function syncProjectLists(MileStone $mileStone, array $projectListIds): void
    {
        ProjectList::where('milestone_id', $mileStone->id)->update(['milestone_id' => null]);
        ProjectList::whereIn('id', $projectListIds)->update(['milestone_id' => $mileStone->id]);
    }

    public function update(MileStone $mileStone, array $attributes): bool
    {
        return $mileStone->fill($attributes)->save();
    }

    public function delete(MileStone $mileStone): bool
    {
        ProjectList::where('milestone_id', $mileStone->id)->update(['milestone_id' => null]);
        return $mileStone->delete();
    }
}
