<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Base;

class ChatMessage extends Base
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'chat_messages';

    protected $fillable = [
        'uuid',
        'chat_id',
        'sender_id',
        'receiver_id',
        'message',
        'is_sent',
        'is_delivered',
        'is_seen',
    ];

    public function chat()
    {
        return $this->belongsTo(Chat::class);
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }
}
