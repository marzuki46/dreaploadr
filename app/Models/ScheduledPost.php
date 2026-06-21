<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScheduledPost extends Model
{
    protected $fillable = [
        'user_id',
        'platform',
        'video_id',
        'ai_content_id',
        'facebook_account_id',
        'facebook_page_id',
        'platform_page_id',
        'description',
        'scheduled_time',
        'status',
        'facebook_post_id',
        'platform_post_id',
        'published_at',
        'error_message',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_time' => 'datetime',
            'published_at' => 'datetime',
        ];
    }

    public function aiContent(): BelongsTo
    {
        return $this->belongsTo(AiContent::class);
    }

    public function video(): BelongsTo
    {
        return $this->belongsTo(Video::class);
    }

    public function facebookAccount(): BelongsTo
    {
        return $this->belongsTo(FacebookAccount::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopePlatform($query, $platform)
    {
        return $query->where('platform', $platform);
    }

    public function isFacebook(): bool
    {
        return $this->platform === 'facebook';
    }

    public function isYouTube(): bool
    {
        return $this->platform === 'youtube';
    }

    public function isTikTok(): bool
    {
        return $this->platform === 'tiktok';
    }
}
