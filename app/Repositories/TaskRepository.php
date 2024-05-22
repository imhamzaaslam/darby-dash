<?php

namespace App\Repositories;

use App\Contracts\AbstractEloquentRepository;
use App\Contracts\TaskRepositoryInterface;
use App\Models\Base;
use App\Models\Task;
use App\Models\Project;
use App\Models\ProjectPhase;
use Illuminate\Support\Collection;

class TaskRepository extends AbstractEloquentRepository implements TaskRepositoryInterface
{
    /**
     * @var Task
     */
    protected Base $model;

    public function __construct(Task $model)
    {
        parent::__construct($model);
    }

    public function get(string $projecId): Collection
    {
        return $this->model->where('project_id', $projecId)->get();
    }

    public function create(ProjectPhase $phase, array $attributes): Task
    {
        $phase_id = $phase->id;
        $project_id = $phase->project_id;

        $data = array_merge($attributes, ['phase_id' => $phase_id, 'project_id' => $project_id]);
        return $this->model->create($data);
    }

    public function update(Task $task, array $attributes): bool
    {
        return $task->fill($attributes)->save();
    }

    public function delete(Task $task): bool
    {
        return $task->delete();
    }
}
