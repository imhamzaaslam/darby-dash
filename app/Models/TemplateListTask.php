<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Base;

class TemplateListTask extends Base
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['uuid', 'template_list_id', 'parent_id' ,'name'];

    public function templateList()
    {
        return $this->belongsTo(TemplateList::class);
    }
}
