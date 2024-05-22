<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Base;

class ProjectBoard extends Base
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'project_id',
        'name',
        'display_order',
    ];
}
