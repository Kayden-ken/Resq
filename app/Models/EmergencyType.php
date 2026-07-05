<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmergencyType extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'color',
        'agency_type',
        'is_active',
        'priority',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'priority' => 'integer',
    ];

    public function requests(): HasMany
    {
        return $this->hasMany(EmergencyRequest::class);
    }

    public function agencies(): HasMany
    {
        return $this->hasMany(EmergencyAgency::class, 'emergency_type_id');
    }
}