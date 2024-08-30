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

    public function getByMorph(string $fileableType, int $fileableId): File|NULL
    {
        return $this->model->where('fileable_type', $fileableType)
            ->where('fileable_id', $fileableId)
            ->orderBy('created_at', 'desc')->first();
    }

    public function getAllByMorph(string $fileableType, int $fileableId): Collection
    {
        return $this->model->where('fileable_type', $fileableType)
            ->where('fileable_id', $fileableId)
            ->get();
    }

    public function deleteByMorph(string $fileableType, int $fileableId): bool
    {
        $files = $this->model->where('fileable_type', $fileableType)
            ->where('fileable_id', $fileableId)
            ->get();
        
        foreach ($files as $file) {
            $disk = $file->path ? $file->path : 'public';
            Storage::disk($disk)->delete($file->path);
        }

        return $this->model->where('fileable_type', $fileableType)
            ->where('fileable_id', $fileableId)
            ->delete();
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
