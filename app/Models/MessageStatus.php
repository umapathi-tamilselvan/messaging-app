<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MessageStatus extends Model
{
    protected $fillable = [
        'message_id',
        'user_id',
        'status',
        'seen_at',
    ];

    protected function casts(): array
    {
        return [
            'seen_at' => 'datetime',
        ];
    }

    public function message()
    {
        return $this->belongsTo(Message::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
