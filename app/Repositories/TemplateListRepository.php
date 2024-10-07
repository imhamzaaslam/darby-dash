<?php

namespace App\Repositories;

use App\Contracts\AbstractEloquentRepository;
use App\Contracts\TemplateListRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Base;
use App\Models\Template;
use App\Models\TemplateList;

class TemplateListRepository  extends AbstractEloquentRepository implements TemplateListRepositoryInterface
{
    /**
     * @var TemplateList
     */
    protected Base $model;

    public function __construct(TemplateList $model)
    {
        parent::__construct($model);
    }

    public function create(array $attributes, Template $template): void
    {

        $list = $this->model->create([
            'name' => $attributes['name'],
            'template_id' => $template->id
        ]);
    }

    public function update(TemplateList $list, array $attributes): bool
    {
        return $list->fill($attributes)->save();
    }

    public function delete(TemplateList $list): bool
    {
        $this->deleteTemplateTasks($list);

        // Delete the list itself
        return $list->delete();
    }

    private function deleteTemplateTasks(TemplateList $list)
    {
        $tasks = $list->templateListTasks;
        foreach ($tasks as $task) {
            $task->delete();
        }
    }

    public function sortLists(array $lists): void
    {
        foreach ($lists as $list) {
            $templateList = $this->model->findOrFail($list['id']);
            if($templateList){
                $templateList->update(['display_order' => $list['order']]);
            }
        }
    }
}
