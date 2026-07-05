<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmergencyContact extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'email',
        'phone',
        'relationship',
        'is_primary',
        'notify_on_emergency',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'notify_on_emergency' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}