<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Base;

class TemplateList extends Base
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['uuid', 'template_id', 'name'];

    public function templateListTasks()
    {
        return $this->hasMany(TemplateListTask::class);
    }

    public function templateListParentTasks()
    {
        return $this->hasMany(TemplateListTask::class, 'template_list_id')->whereNull('parent_id');
    }

    public function template()
    {
        return $this->belongsTo(Template::class);
    }
}
