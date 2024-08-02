<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Base;

class MileStone extends Base
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'milestones';

    protected $fillable = [
        'name',
        'project_id',
        'display_order',
    ];

    public function lists()
    {
        return $this->hasMany(ProjectList::class, 'milestone_id');
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
