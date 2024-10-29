<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Base;

class Chat extends Base
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'chats';

    protected $fillable = [
        'uuid',
        'project_id',
        'user_id',
        'unseen_msgs',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function messages()
    {
        return $this->hasMany(ChatMessage::class);
    }

    public function contact()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
