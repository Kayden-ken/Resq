<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Announcement extends Model
{
    protected $fillable = [
        'title',
        'content',
        'type',
        'priority',
        'target_audience',
        'starts_at',
        'expires_at',
        'created_by',
        'is_active',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    // Type Constants
    const TYPE_GENERAL = 'general';
    const TYPE_EMERGENCY = 'emergency';
    const TYPE_MAINTENANCE = 'maintenance';
    const TYPE_UPDATE = 'update';

    // Priority Constants
    const PRIORITY_LOW = 'low';
    const PRIORITY_NORMAL = 'normal';
    const PRIORITY_HIGH = 'high';
    const PRIORITY_URGENT = 'urgent';

    // Target Audience Constants
    const AUDIENCE_ALL = 'all';
    const AUDIENCE_USERS = 'users';
    const AUDIENCE_RESPONDERS = 'responders';
    const AUDIENCE_ADMINS = 'admins';

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where('starts_at', '<=', now())
            ->where(function ($q) {
                $q->where('expires_at', '>=', now())
                    ->orWhereNull('expires_at');
            });
    }

    public function isCurrentlyActive(): bool
    {
        return $this->is_active
            && $this->starts_at <= now()
            && ($this->expires_at === null || $this->expires_at >= now());
    }
}