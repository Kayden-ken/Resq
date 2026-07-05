<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Facility extends Model
{
    protected $fillable = [
        'name',
        'type',
        'agency_id',
        'phone',
        'email',
        'address',
        'latitude',
        'longitude',
        'city',
        'state',
        'country',
        'postal_code',
        'website',
        'capacity',
        'is_24_hours',
        'is_active',
        'beds_available',
    ];

    protected $casts = [
        'latitude' => 'double',
        'longitude' => 'double',
        'is_24_hours' => 'boolean',
        'is_active' => 'boolean',
        'capacity' => 'integer',
        'beds_available' => 'integer',
    ];

    // Facility Type Constants
    const TYPE_HOSPITAL = 'hospital';
    const TYPE_POLICE_STATION = 'police_station';
    const TYPE_FIRE_STATION = 'fire_station';
    const TYPE_EVACUATION_CENTER = 'evacuation_center';
    const TYPE_RESCUE_CENTER = 'rescue_center';
    const TYPE_CLINIC = 'clinic';
    const TYPE_AMBULANCE = 'ambulance';

    public function agency(): BelongsTo
    {
        return $this->belongsTo(EmergencyAgency::class, 'agency_id');
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

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeNearby($query, float $lat, float $lng, int $radius = 50)
    {
        return $query->where('is_active', true)
            ->orderBy('name')
            ->limit(10);
    }
}