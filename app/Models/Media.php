<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Media extends Model
{
    protected $fillable = [
        'incident_id',
        'uploader_id',
        'media_type',
        'file_name',
        'file_path',
        'file_size',
        'mime_type',
        'description',
    ];

    protected $casts = [
        'file_size' => 'integer',
    ];

    // Media Type Constants
    const TYPE_IMAGE = 'image';
    const TYPE_VIDEO = 'video';
    const TYPE_AUDIO = 'audio';
    const TYPE_DOCUMENT = 'document';

    public function incident(): BelongsTo
    {
        return $this->belongsTo(EmergencyRequest::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploader_id');
    }

    public function getUrlAttribute(): string
    {
        return asset('storage/' . $this->file_path);
    }

    public function getIconAttribute(): string
    {
        return match($this->media_type) {
            self::TYPE_IMAGE => 'image',
            self::TYPE_VIDEO => 'video',
            self::TYPE_AUDIO => 'microphone',
            self::TYPE_DOCUMENT => 'document',
            default => 'file',
        };
    }
}