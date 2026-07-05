<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmergencyAgency extends Model
{
    protected $fillable = [
        'name',
        'type',
        'emergency_type_id',
        'phone',
        'email',
        'address',
        'latitude',
        'longitude',
        'website',
        'description',
        'is_active',
    ];

    protected $casts = [
        'latitude' => 'double',
        'longitude' => 'double',
        'is_active' => 'boolean',
    ];

    // Agency Type Constants
    const TYPE_POLICE = 'police';
    const TYPE_FIRE = 'fire';
    const TYPE_AMBULANCE = 'ambulance';
    const TYPE_RESCUE = 'rescue';
    const TYPE_HOSPITAL = 'hospital';
    const TYPE_DISPATCH = 'dispatch';

    public function emergencyType(): BelongsTo
    {
        return $this->belongsTo(EmergencyType::class);
    }

    public function responders(): HasMany
    {
        return $this->hasMany(Responder::class, 'agency_id');
    }

    public function facilities(): HasMany
    {
        return $this->hasMany(Facility::class, 'agency_id');
    }
}