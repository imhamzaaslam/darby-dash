<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Base;

class File extends Base
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'uuid',
        'name',
        'path',
        'url',
        'type',
        'size',
        'project_id',
        'folder_id',
        'display_order',
        'created_by',
        'updated_by',
        'deleted_by',
    ];
}
