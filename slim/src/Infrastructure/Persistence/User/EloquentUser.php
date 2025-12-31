<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\User;

use Illuminate\Database\Eloquent\Model;

class EloquentUser extends Model
{
    protected $table = 'users';
    public $timestamps = false;
    protected $fillable = [
        'username',
        'first_name',
        'last_name',
    ];
    protected $casts = [
        'id' => 'integer',
    ];
}

