<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Responder extends Model
{
    protected $fillable = [
        'user_id',
        'badge_number',
        'agency_id',
        'status',
        'latitude',
        'longitude',
        'current_location_address',
        'availability_status',
        'vehicle_info',
        'shift_start',
        'shift_end',
        'is_on_duty',
    ];

    protected $casts = [
        'latitude' => 'double',
        'longitude' => 'double',
        'is_on_duty' => 'boolean',
        'shift_start' => 'datetime',
        'shift_end' => 'datetime',
    ];

    // Status Constants
    const STATUS_AVAILABLE = 'available';
    const STATUS_BUSY = 'busy';
    const STATUS_OFFLINE = 'offline';
    const STATUS_ON_DUTY = 'on_duty';

    // Availability Constants
    const AVAILABILITY_AVAILABLE = 'available';
    const AVAILABILITY_BUSY = 'busy';
    const AVAILABILITY_OFFLINE = 'offline';

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function agency(): BelongsTo
    {
        return $this->belongsTo(EmergencyAgency::class, 'agency_id');
    }

    public function incidents(): HasMany
    {
        return $this->hasMany(IncidentResponder::class, 'responder_id');
    }

    public function assignedIncidents(): HasMany
    {
        return $this->hasMany(IncidentResponder::class, 'responder_id')
            ->whereIn('status', [IncidentResponder::STATUS_ACCEPTED, IncidentResponder::STATUS_RESPONDING, IncidentResponder::STATUS_ARRIVED]);
    }

    // Scopes
    public function scopeAvailable($query)
    {
        return $query->where('availability_status', self::AVAILABILITY_AVAILABLE)
            ->where('is_on_duty', true);
    }

    public function scopeOnDuty($query)
    {
        return $query->where('is_on_duty', true);
    }

    // Helper Methods
    public function isAvailable(): bool
    {
        return $this->availability_status === self::AVAILABILITY_AVAILABLE && $this->is_on_duty;
    }

    public function updateLocation(float $latitude, float $longitude, ?string $address = null): void
    {
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        if ($address) {
            $this->current_location_address = $address;
        }
        $this->save();
    }
}