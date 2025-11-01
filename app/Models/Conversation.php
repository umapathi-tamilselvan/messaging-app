<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Conversation extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'type',
        'name',
        'avatar_url',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'created_by' => 'integer',
        ];
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'conversation_user')
            ->withPivot('joined_at', 'last_read_at', 'unread_count')
            ->withTimestamps();
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function latestMessage()
    {
        return $this->hasOne(Message::class)->latestOfMany();
    }
}
