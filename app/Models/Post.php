<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = [
        'author_id', 'slug', 'title', 'excerpt', 'body', 'published_at', 'is_published'
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'is_published' => 'boolean',
        ];
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}



