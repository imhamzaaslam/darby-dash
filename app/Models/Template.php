<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Base;

class Template extends Base
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['uuid', 'template_name', 'project_id'];

    public function templateLists()
    {
        return $this->hasMany(TemplateList::class)->orderBy('display_order');
    }

    function scopeFiltered(Builder $query, ?string $keyword): Builder
    {
        if ($keyword) {
            return $query->where(function ($query) use ($keyword) {
                $query->where('template_name', 'LIKE', "%{$keyword}%");
            });
        }

        return $query;
    }
}
