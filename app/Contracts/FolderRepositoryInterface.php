<?php

namespace App\Contracts;

use App\Models\Folder;
use App\Models\Project;
use Illuminate\Support\Collection;

interface FolderRepositoryInterface
{
    public function create(Project $project, array $attributes): Folder;

    public function update(Folder $folder, array $attributes): bool;

    public function delete(Folder $folder): bool;
}