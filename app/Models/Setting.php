<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kodeine\Metable\Metable;
use Illuminate\Database\Eloquent\Model;
use App\Models\Base;

class Setting extends Base
{
    protected $metaTable = 'settings_meta';
    use Metable, HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'name',
    ];
}
