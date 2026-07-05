<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    protected $fillable = [
        'user_id',
        'incident_id',
        'type',
        'title',
        'message',
        'data',
        'channel',
        'is_sent',
        'sent_at',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'data' => 'array',
        'is_sent' => 'boolean',
        'sent_at' => 'datetime',
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    // Notification Type Constants
    const TYPE_EMERGENCY = 'emergency';
    const TYPE_STATUS = 'status';
    const TYPE_RESPONDER = 'responder';
    const TYPE_SYSTEM = 'system';
    const TYPE_DISASTER = 'disaster';

    // Channel Constants
    const CHANNEL_PUSH = 'push';
    const CHANNEL_SMS = 'sms';
    const CHANNEL_EMAIL = 'email';
    const CHANNEL_IN_APP = 'in_app';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function incident(): BelongsTo
    {
        return $this->belongsTo(EmergencyRequest::class);
    }

    public function markAsRead(): void
    {
        $this->is_read = true;
        $this->read_at = now();
        $this->save();
    }

    public function markAsSent(): void
    {
        $this->is_sent = true;
        $this->sent_at = now();
        $this->save();
    }
}