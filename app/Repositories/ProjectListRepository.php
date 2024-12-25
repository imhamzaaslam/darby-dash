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

    public function getProjectLists(Project $project, array $filters): Collection
    {
        return $project->lists()->with(['tasks' => function ($query) use ($filters) {
            if (!empty($filters)) {
                $query->filter($filters);
            }
        }])
        ->orderByRaw('
            CASE 
                WHEN display_order = 0 THEN created_at 
                ELSE display_order 
            END ASC
        ')->get();
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
        // delete related entities
        $this->deleteTasks($projectList);
        return $projectList->delete();
    }

    public function deleteTasks(ProjectList $projectList): void
    {
        $tasks = $projectList->allTasks;
        foreach ($tasks as $task) {
            $task->files()->delete();
            $task->delete();
        }
    }


    public function getListTasks(ProjectList $projectList): Collection
    {
        return $projectList->tasks;
    }

    public function getListWithoutMilestone(Project $project): Collection
    {
        return $project->lists->where('milestone_id', null);
    }

    public function sortLists(array $lists): void
    {
        foreach ($lists as $list) {
            $projectList = $this->model->findOrFail($list['id']);
            if($projectList){
                $projectList->update(['display_order' => $list['order']]);
            }
        }
    }
}
