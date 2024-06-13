<?php

namespace App\Contracts;

use App\Models\MileStone;
use App\Models\Folder;
use App\Models\File;
use App\Models\Project;
use App\Helpers\FileData;
use Illuminate\Support\Collection;

interface FileRepositoryInterface
{
    public function store(string $fileableType, int $fileableId, FileData $fileData): ?File;

    public function update(string $fileUuid, string $fileableType, int $fileableId, FileData $fileData): ?File;

    public function getByMorph(string $fileableType, int $fileableId): File|NULL;

    public function getAllByMorph(string $fileableType, int $fileableId): Collection;

    public function deleteByMorph(string $fileableType, int $fileableId): bool;

    public function create(array $attributes): File;

    public function delete(File $file): bool;
}