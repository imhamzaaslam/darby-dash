<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Base;

class ProjectMember extends Base
{
    use HasFactory;
    use SoftDeletes;
    
    protected $fillable = [
        'uuid',
        'project_id',
        'user_id',
        'role_id',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
