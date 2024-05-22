<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Base;

class ProjectPhase extends Base
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'project_id',
        'name',
        'display_order',
        
    ];
}
