<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Base;

class Template extends Base
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['uuid', 'template_name', 'project_id'];

    public function templateLists()
    {
        return $this->hasMany(TemplateList::class);
    }
}
