<?php

namespace App\Contracts;
use App\Models\TemplateListTask;
use App\Models\TemplateList;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;

interface TemplateTaskRepositoryInterface
{
    public function create(array $attributes, TemplateList $list): void;
    public function update(TemplateListTask $task, array $attributes): bool;
    public function delete(TemplateListTask $task): bool;
}
