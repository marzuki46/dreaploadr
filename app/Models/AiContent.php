<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AiContent extends Model
{
    protected $table = 'ai_contents';

    protected $fillable = [
        'user_id',
        'video_id',
        'original_text',
        'ai_remake_text',
        'ai_provider',
        'status',
    ];

    public function video(): BelongsTo
    {
        return $this->belongsTo(Video::class);
    }

    public function scheduledPosts(): HasMany
    {
        return $this->hasMany(ScheduledPost::class);
    }
}