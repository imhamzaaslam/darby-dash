<?php

namespace App\Contracts;

use App\Models\MileStone;
use App\Models\Folder;
use App\Models\File;
use App\Models\Project;
use Illuminate\Support\Collection;

interface FileRepositoryInterface
{
    public function getProjectFiles(Project $project): Collection;

    public function create(array $attributes): File;

    public function delete(File $file): bool;
}