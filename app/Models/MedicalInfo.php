<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedicalInfo extends Model
{
    protected $fillable = [
        'user_id',
        'blood_type',
        'allergies',
        'medical_conditions',
        'medications',
        'medical_notes',
        'organ_donor',
        'emergency_medical_id',
    ];

    protected $casts = [
        'allergies' => 'array',
        'medical_conditions' => 'array',
        'medications' => 'array',
        'organ_donor' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function bloodTypes(): array
    {
        return ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-', 'Unknown'];
    }
}