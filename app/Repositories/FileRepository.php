<?php

namespace App\Repositories;

use App\Contracts\AbstractEloquentRepository;
use App\Contracts\FileRepositoryInterface;
use App\Enums\FileType;
use App\Helpers\FileData;
use App\Models\File;

class FileRepository extends AbstractEloquentRepository implements FileRepositoryInterface
{
    protected \App\Models\Base $model;

    public function __construct(File $model)
    {
        parent::__construct($model);
    }

    public function store(string $fileableType, int $fileableId, FileData $fileData): ?File
    {
        return $this->model->create([
            'fileable_type' => $fileableType,
            'fileable_id' => $fileableId,
            'name' => $fileData->name,
            'mime_type' => $fileData->mimeType,
            'type' => FileType::AVATAR,
            'path' => $fileData->path,
            'url' => $fileData->url,
            'size' => $fileData->size,
        ]);
    }

    public function update(string $fileUuid, string $fileableType, int $fileableId, FileData $fileData): ?File
    {
        $file = $this->getByUuid($fileUuid);
        $file->fill([
            'fileable_type' => $fileableType,
            'fileable_id' => $fileableId,
            'name' => $fileData->name,
            'mime_type' => $fileData->mimeType,
            'type' => FileType::AVATAR,
            'path' => $fileData->path,
            'url' => $fileData->url,
            'size' => $fileData->size,
        ])->save();
        return $file;
    }
}
