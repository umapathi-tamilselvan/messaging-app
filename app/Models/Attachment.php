<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    protected $fillable = [
        'message_id',
        'path',
        'thumbnail_path',
        'low_bandwidth_url',
        'mime_type',
        'size',
        'width',
        'height',
        'duration',
    ];

    protected function casts(): array
    {
        return [
            'size' => 'integer',
            'width' => 'integer',
            'height' => 'integer',
            'duration' => 'integer',
        ];
    }

    public function message()
    {
        return $this->belongsTo(Message::class);
    }
}
