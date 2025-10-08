<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupportReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'subject',
        'description',
        'email',
        'paste_identifier',
        'copyright_work',
        'authorization',
        'contact_info',
        'violation_type',
        'status',
        'admin_notes',
        'resolved_at',
        'resolved_by',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
    ];

    public function resolver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'dmca' => 'DMCA Takedown Notice',
            'abuse' => 'Abuse Report',
            'general' => 'General Support',
            'security' => 'Security Issue',
            'appeal' => 'Policy Appeal',
            default => ucfirst($this->type),
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending' => 'Pending',
            'in_progress' => 'In Progress',
            'resolved' => 'Resolved',
            'closed' => 'Closed',
            default => ucfirst($this->status),
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'pending' => 'text-yt-warning',
            'in_progress' => 'text-yt-accent',
            'resolved' => 'text-yt-success',
            'closed' => 'text-yt-text-disabled',
            default => 'text-yt-text',
        };
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeResolved($query)
    {
        return $query->where('status', 'resolved');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }
}