<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Base;

class CalendarEvent extends Base
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'name',
        'description',
        'start_date',
        'end_date',
        'url',
        'project_id',
        'display_order',
    ];

    public function guests()
    {
        return $this->belongsToMany(User::class, 'calendar_event_guests', 'calendar_event_id', 'guest_id')->withTimestamps();
    }

}
