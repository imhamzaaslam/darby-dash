<?php

namespace App\Contracts;
use App\Models\TemplateList;
use App\Models\Template;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;

interface TemplateListRepositoryInterface
{
    public function create(array $attributes, Template $template): void;
    public function update(TemplateList $list, array $attributes): bool;
    public function delete(TemplateList $list): bool;
    public function sortLists(array $lists): void;
}
