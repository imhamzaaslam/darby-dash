<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Base;

class ProjectList extends Base
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'project_id',
        'name',
        'display_order',
    ];

    public function tasks()
    {
        return $this->hasMany(Task::class, 'list_id');
    }
}
