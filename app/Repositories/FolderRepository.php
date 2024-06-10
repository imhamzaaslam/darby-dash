<?php

namespace App\Repositories;

use App\Contracts\AbstractEloquentRepository;
use App\Contracts\FolderRepositoryInterface;
use App\Models\Base;
use App\Models\Folder;
use App\Models\Project;

class FolderRepository extends AbstractEloquentRepository implements FolderRepositoryInterface
{
    /**
     * @var MileStone
     */
    protected Base $model;

    public function __construct(Folder $model)
    {
        parent::__construct($model);
    }

    public function create(Project $project, array $attributes): Folder
    {
        return $this->model->create(array_merge($attributes, ['project_id' => $project->id]));
    }

    public function update(Folder $folder, array $attributes): bool
    {
        return $folder->fill($attributes)->save();
    }

    public function delete(Folder $folder): bool
    {
        return $folder->delete();
    }
}
