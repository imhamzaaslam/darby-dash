<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Base;

class Settings_meta extends Base
{
    protected $table = 'settings_meta';
    use HasFactory;

    protected $fillable = ['type', 'user_id', 'setting_id', 'key' ,'value', 'deliverable_channel'];
}
