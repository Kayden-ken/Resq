<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserProfile extends Model
{
    protected $fillable = [
        'user_id',
        'date_of_birth',
        'gender',
        'address',
        'city',
        'state',
        'country',
        'postal_code',
        'avatar',
        'notify_sos',
        'notify_emergency',
        'notify_status',
        'notify_news',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'notify_sos' => 'boolean',
        'notify_emergency' => 'boolean',
        'notify_status' => 'boolean',
        'notify_news' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getFullAddressAttribute(): string
    {
        $parts = array_filter([
            $this->address,
            $this->city,
            $this->state,
            $this->postal_code,
            $this->country,
        ]);

        return implode(', ', $parts);
    }
}