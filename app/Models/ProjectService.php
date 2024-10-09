<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\FileType;
use App\Models\Base;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectService extends Base
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'uuid',
        'project_type_id',
        'title',
        'description',
        'status',
        'display_order',
    ];

    public function projectType()
    {
        return $this->belongsTo(ProjectType::class);
    }

    public function serviceImage(): ?MorphOne
    {
        return $this->morphOne(File::class, 'fileable')
            ->where('type', FileType::AVATAR->value)
            ->latest();
    }

    function scopeFiltered(Builder $query, ?string $projectTypeId): Builder
    {
        $projectServicesTable = (new ProjectService())->getTable();

        // check project type in service table
        return $query->when($projectTypeId, function ($query, $projectTypeId) use ($projectServicesTable) {
            return $query->where($projectServicesTable . '.project_type_id', $projectTypeId);
        });
    }
}
