<?php

namespace App\Contracts;
use App\Models\Template;
use App\Models\Project;
use Illuminate\Support\Collection;

interface TemplateRepositoryInterface
{
    public function get(): Collection;
    public function getTemplate(int $id): Template;
    public function create(array $attributes, Project $project):Template;
    public function createProjectListAndTask(Template $template, Project $project);
    public function hasTemplateLists(Template $template): bool;
}
