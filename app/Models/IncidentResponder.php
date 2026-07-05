<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IncidentResponder extends Model
{
    protected $fillable = [
        'incident_id',
        'responder_id',
        'agency_id',
        'status',
        'assigned_at',
        'accepted_at',
        'estimated_arrival',
        'arrived_at',
        'completed_at',
        'notes',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'accepted_at' => 'datetime',
        'estimated_arrival' => 'datetime',
        'arrived_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    // Status Constants
    const STATUS_PENDING = 'pending';
    const STATUS_ACCEPTED = 'accepted';
    const STATUS_RESPONDING = 'responding';
    const STATUS_ARRIVED = 'arrived';
    const STATUS_COMPLETED = 'completed';
    const STATUS_REJECTED = 'rejected';

    public function incident(): BelongsTo
    {
        return $this->belongsTo(EmergencyRequest::class, 'incident_id');
    }

    public function responder(): BelongsTo
    {
        return $this->belongsTo(Responder::class);
    }

    public function agency(): BelongsTo
    {
        return $this->belongsTo(EmergencyAgency::class, 'agency_id');
    }

    public function accept(): void
    {
        $this->status = self::STATUS_ACCEPTED;
        $this->accepted_at = now();
        $this->save();
    }

    public function reject(): void
    {
        $this->status = self::STATUS_REJECTED;
        $this->save();
    }

    public function startResponding(): void
    {
        $this->status = self::STATUS_RESPONDING;
        $this->save();
    }

    public function markArrived(): void
    {
        $this->status = self::STATUS_ARRIVED;
        $this->arrived_at = now();
        $this->save();
    }

    public function markCompleted(): void
    {
        $this->status = self::STATUS_COMPLETED;
        $this->completed_at = now();
        $this->save();
    }
}