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
    ];

    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}

