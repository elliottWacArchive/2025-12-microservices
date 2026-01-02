<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Message;

use Illuminate\Database\Eloquent\Model;

class EloquentMessage extends Model
{
    protected $table = 'messages';

    public $timestamps = true;

    protected $fillable = [
        'user_id',
        'content',
        'discussion_id',
    ];

    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'discussion_id' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}

