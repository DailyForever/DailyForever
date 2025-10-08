<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prekey extends Model
{
    protected $fillable = [
        'user_id', 'kid', 'alg', 'public_key', 'used_at',
    ];

    protected function casts(): array
    {
        return [
            'used_at' => 'datetime',
        ];
    }
}


