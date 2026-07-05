<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'user_type', // user, responder, dispatcher, admin
        'is_active',
        'email_verified_at',
        'phone_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'phone_verified_at' => 'datetime',
        'is_active' => 'boolean',
        'password' => 'hashed',
    ];

    // User Types Constants
    const TYPE_USER = 'user';
    const TYPE_RESPONDER = 'responder';
    const TYPE_DISPATCHER = 'dispatcher';
    const TYPE_ADMIN = 'admin';

    // Relationships
    public function profile(): HasOne
    {
        return $this->hasOne(UserProfile::class);
    }

    public function emergencyContacts(): HasMany
    {
        return $this->hasMany(EmergencyContact::class, 'user_id');
    }

    public function medicalInfo(): HasOne
    {
        return $this->hasOne(MedicalInfo::class);
    }

    public function emergencyRequests(): HasMany
    {
        return $this->hasMany(EmergencyRequest::class, 'requester_id');
    }

    public function responderProfile(): HasOne
    {
        return $this->hasOne(Responder::class);
    }

    public function sentMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function receivedMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    public function feedbackGiven(): HasMany
    {
        return $this->hasMany(Feedback::class, 'user_id');
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class, 'user_id');
    }

    // Scopes
    public function scopeUsers($query)
    {
        return $query->where('user_type', self::TYPE_USER);
    }

    public function scopeResponders($query)
    {
        return $query->where('user_type', self::TYPE_RESPONDER);
    }

    public function scopeAdmins($query)
    {
        return $query->whereIn('user_type', [self::TYPE_ADMIN, self::TYPE_DISPATCHER]);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Helper Methods
    public function isResponder(): bool
    {
        return $this->user_type === self::TYPE_RESPONDER;
    }

    public function isAdmin(): bool
    {
        return in_array($this->user_type, [self::TYPE_ADMIN, self::TYPE_DISPATCHER]);
    }

    public function canAccessAdmin(): bool
    {
        return in_array($this->user_type, [self::TYPE_ADMIN, self::TYPE_DISPATCHER]);
    }
}