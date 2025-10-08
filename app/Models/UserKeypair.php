<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class UserKeypair extends Model
{
    protected $fillable = [
        'user_id',
        'key_id',
        'public_key',
        'secret_key',
        'algorithm',
        'is_active',
        'expires_at',
        'rotation_from',
        'rotation_to',
        'rotation_status',
        'rotation_initiated_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'expires_at' => 'datetime',
        'rotation_initiated_at' => 'datetime',
        'public_key' => 'encrypted',
        'secret_key' => 'encrypted',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function generateKeyId(): string
    {
        return 'kyber_' . Str::random(32);
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function isUsable(): bool
    {
        return $this->is_active && !$this->isExpired();
    }

    public function getPublicKeyBytes(): string
    {
        return $this->public_key;
    }

    public function getSecretKeyBytes(): string
    {
        return $this->secret_key;
    }
}