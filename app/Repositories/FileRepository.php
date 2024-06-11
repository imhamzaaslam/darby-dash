<?php

namespace App\Repositories;

use App\Contracts\AbstractEloquentRepository;
use App\Contracts\FileRepositoryInterface;
use App\Enums\FileType;
use App\Helpers\FileData;
use App\Models\Base;
use App\Models\File;
use App\Models\Folder;
use App\Models\Project;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Collection;

class FileRepository extends AbstractEloquentRepository implements FileRepositoryInterface
{
    /**
     * @var File
     */
    protected Base $model;

    public function __construct(File $model)
    {
        parent::__construct($model);
    }

    public function getProjectFiles(Project $project): Collection
    {
        return $project->files()->whereNull('folder_id')->get();
    }

    public function create(array $attributes): File
    {
        return $this->model->create($attributes);
    }

    public function delete(File $file): bool
    {
        return $file->delete();
    }
}
