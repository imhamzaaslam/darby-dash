<?php

namespace App\Repositories;

use App\Contracts\AbstractEloquentRepository;
use App\Contracts\ProjectServiceRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Base;
use App\Models\ProjectType;
use App\Models\ProjectService;

class ProjectServiceRepository  extends AbstractEloquentRepository implements ProjectServiceRepositoryInterface
{
    /**
     * @var ProjectService
     */
    protected Base $model;

    public function __construct(ProjectService $model)
    {
        parent::__construct($model);
    }

    public function getServicesQuery(): Builder
    {
        return $this->model->whereNotNull('project_type_id');
    }

    public function getByType(ProjectType $projectType): Collection
    {
        return $this->model->where('project_type_id', $projectType->id)
        ->where('status', 1)->get();
    }

    public function create(array $attributes): ProjectService
    {

        $service = $this->model->create([
            'title' => $attributes['title'],
            'project_type_id' => $attributes['project_type_id'],
            'description' => $attributes['description'],
            'status' => $attributes['status'],
        ]);

        return $service;
    }

    public function update(ProjectService $service, array $attributes): bool
    {
        return $service->fill($attributes)->save();
    }

    public function delete(ProjectService $service): bool
    {
        $this->deleteServiceImage($service);
        return $service->delete();
    }

    private function deleteServiceImage(ProjectService $service)
    {
        $service->serviceImage()->delete();
    }

    public function sortServices(array $services): void
    {
        foreach ($services as $item) {
            $service = $this->model->findOrFail($item['id']);
            if($service){
                $service->update(['display_order' => $item['order']]);
            }
        }
    }
}
