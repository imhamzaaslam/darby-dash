<?php

namespace App\Contracts;
use App\Models\Template;
use App\Models\Project;
use Illuminate\Support\Collection;

interface TemplateRepositoryInterface
{
    public function get(): Collection;
    public function create(array $attributes, Project $project):Template;
}
