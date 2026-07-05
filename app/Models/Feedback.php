<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Feedback extends Model
{
    protected $fillable = [
        'incident_id',
        'user_id',
        'responder_id',
        'rating',
        'comment',
        'is_anonymous',
    ];

    protected $casts = [
        'rating' => 'integer',
        'is_anonymous' => 'boolean',
    ];

    public function incident(): BelongsTo
    {
        return $this->belongsTo(EmergencyRequest::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function responder(): BelongsTo
    {
        return $this->belongsTo(Responder::class);
    }
}