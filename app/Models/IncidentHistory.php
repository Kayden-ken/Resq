<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IncidentHistory extends Model
{
    protected $table = 'incident_history';
    public $timestamps = false;

    protected $fillable = [
        'incident_id',
        'status',
        'notes',
        'changed_by',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function incident(): BelongsTo
    {
        return $this->belongsTo(EmergencyRequest::class, 'incident_id');
    }

    public function changer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}