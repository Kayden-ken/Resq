<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class EmergencyRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'requester_id',
        'emergency_type_id',
        'incident_number',
        'status',
        'latitude',
        'longitude',
        'address',
        'description',
        'severity', // low, medium, high, critical
        'is_sos',
        'is_verified',
        'verification_note',
        'assigned_dispatcher_id',
        'estimated_arrival',
        'actual_arrival',
        'completed_at',
    ];

    protected $casts = [
        'latitude' => 'double',
        'longitude' => 'double',
        'is_sos' => 'boolean',
        'is_verified' => 'boolean',
        'estimated_arrival' => 'datetime',
        'actual_arrival' => 'datetime',
        'completed_at' => 'datetime',
    ];

    // Status Constants
    const STATUS_PENDING = 'pending';
    const STATUS_ACCEPTED = 'accepted';
    const STATUS_RESPONDING = 'responding';
    const STATUS_ARRIVED = 'arrived';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_REJECTED = 'rejected';

    // Severity Constants
    const SEVERITY_LOW = 'low';
    const SEVERITY_MEDIUM = 'medium';
    const SEVERITY_HIGH = 'high';
    const SEVERITY_CRITICAL = 'critical';

    // Relationships
    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function emergencyType(): BelongsTo
    {
        return $this->belongsTo(EmergencyType::class);
    }

    public function assignedDispatcher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_dispatcher_id');
    }

    public function responders(): HasMany
    {
        return $this->hasMany(IncidentResponder::class, 'incident_id');
    }

    public function assignedResponders(): HasMany
    {
        return $this->hasMany(IncidentResponder::class, 'incident_id')->where('status', '!=', IncidentResponder::STATUS_REJECTED);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class, 'incident_id');
    }

    public function media(): HasMany
    {
        return $this->hasMany(Media::class, 'incident_id');
    }

    public function history(): HasMany
    {
        return $this->hasMany(IncidentHistory::class, 'incident_id');
    }

    public function feedback(): HasOne
    {
        return $this->hasOne(Feedback::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class, 'incident_id');
    }

    protected static function booted()
    {
        static::creating(function (self $request) {
            if (empty($request->incident_number)) {
                $request->incident_number = 'INC-' . strtoupper(Str::random(8));
            }
        });
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', [self::STATUS_PENDING, self::STATUS_ACCEPTED, self::STATUS_RESPONDING, self::STATUS_ARRIVED]);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    public function scopeSos($query)
    {
        return $query->where('is_sos', true);
    }

    public function scopeUnverified($query)
    {
        return $query->where('is_verified', false);
    }

    // Helper Methods
    public function isActive(): bool
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_ACCEPTED, self::STATUS_RESPONDING, self::STATUS_ARRIVED]);
    }

    public function canBeModified(): bool
    {
        return in_array($this->status, [self::STATUS_PENDING]);
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PENDING => 'yellow',
            self::STATUS_ACCEPTED => 'blue',
            self::STATUS_RESPONDING => 'indigo',
            self::STATUS_ARRIVED => 'green',
            self::STATUS_COMPLETED => 'gray',
            self::STATUS_CANCELLED => 'red',
            self::STATUS_REJECTED => 'red',
            default => 'gray',
        };
    }

    public function getSeverityColorAttribute(): string
    {
        return match($this->severity) {
            self::SEVERITY_LOW => 'green',
            self::SEVERITY_MEDIUM => 'yellow',
            self::SEVERITY_HIGH => 'orange',
            self::SEVERITY_CRITICAL => 'red',
            default => 'gray',
        };
    }
}