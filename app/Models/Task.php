<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Base;

class Task extends Base
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'uuid',
        'name',
        'description',
        'status',
        'project_id',
        'parent_id',
        'list_id',
        'display_order',
        'start_date',
        'due_date',
        'est_time',
    ];

    protected $casts = [
        'due_date' => 'datetime',
    ];

    public function subtasks()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function getStatus()
    {
        return $this->belongsTo(Status::class, 'status');
    }
}
