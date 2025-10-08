<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    protected $fillable = [
        'identifier',
        'paste_id',
        'user_id',
        'recipient_id',
        'original_filename',
        'mime_type',
        'size_bytes',
        'views',
        'view_limit',
        'is_private',
        'expires_at',
        'storage_path',
        'iv',
        'kem_alg',
        'kem_kid',
        'kem_ct',
        'kem_wrapped_key',
        'password_hash',
        'password_hint',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
        ];
    }

    public static function generateIdentifier(): string
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

    public function paste()
    {
        return $this->belongsTo(Paste::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return 'identifier';
    }
}


