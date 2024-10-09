<?php

namespace App\Contracts;
use App\Models\ProjectService;
use App\Models\ProjectType;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;

interface ProjectServiceRepositoryInterface
{
    public function getServicesQuery(): Builder;
    public function getByType(ProjectType $projectType):Collection;
    public function create(array $attributes):ProjectService;
    public function update(ProjectService $service, array $attributes): bool;
    public function delete(ProjectService $service): bool;
    public function sortServices(array $services): void;
}
