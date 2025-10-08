<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Paste extends Model
{
    protected $fillable = [
        'identifier',
        'encrypted_content',
        'iv',
        'expires_at',
        'views',
        'user_id',
        'uploader_ip',
        'is_removed',
        'removed_reason',
        'removed_at',
        'removed_by',
        'view_limit',
        'is_private',
        'kem_alg',
        'kem_kid',
        'kem_ct',
        'kem_wrapped_key',
        'recipient_id',
        'password_hash',
        'password_hint',
        'encryption_key'
    ];

    protected $dates = [
        'expires_at'
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'encryption_key' => 'encrypted',
        ];
    }

    public static function generateIdentifier()
    {
        $alphabet = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
        $length = 6;
        do {
            $id = '';
            $bytes = random_bytes($length);
            for ($i = 0; $i < $length; $i++) {
                $id .= $alphabet[ord($bytes[$i]) % strlen($alphabet)];
            }
            $exists = self::where('identifier', $id)->exists();
        } while ($exists);
        return $id;
    }

    public function isExpired()
    {
        return $this->expires_at && Carbon::now()->greaterThan($this->expires_at);
    }

    public function incrementViews()
    {
        if ($this->view_limit !== null && $this->views >= $this->view_limit) {
            return;
        }
        $this->increment('views');
    }

    public function hasReachedViewLimit(): bool
    {
        return $this->view_limit !== null && $this->views >= $this->view_limit;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function files()
    {
        return $this->hasMany(File::class);
    }
}
