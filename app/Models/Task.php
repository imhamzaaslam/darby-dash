<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Base;

class Task extends Base
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'name',
        'description',
        'status',
        'project_id',
        'phase_id',
        'start_date',
        'due_date',
        'time_spent',
    ];
}
