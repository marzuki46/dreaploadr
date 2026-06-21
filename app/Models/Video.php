<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Video extends Model
{
    protected $fillable = [
        'user_id',
        'facebook_account_id',
        'facebook_page_id',
        'facebook_video_id',
        'source_url',
        'thumbnail_url',
        'title',
        'author_name',
        'description',
        'keywords',
        'source_type',
        'search_query',
        'status',
    ];

    public function scopeSearch($query, ?string $term, ?string $sourceType = null, ?string $accountId = null)
    {
        if ($term) {
            $query->where(function ($q) use ($term) {
                $q->where('title', 'like', "%{$term}%")
                  ->orWhere('description', 'like', "%{$term}%")
                  ->orWhere('keywords', 'like', "%{$term}%")
                  ->orWhere('author_name', 'like', "%{$term}%")
                  ->orWhere('facebook_video_id', 'like', "%{$term}%")
                  ->orWhere('search_query', 'like', "%{$term}%");
            });
        }

        if ($sourceType) {
            $query->where('source_type', $sourceType);
        }

        if ($accountId) {
            $query->where('facebook_account_id', $accountId);
        }

        return $query;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function facebookAccount(): BelongsTo
    {
        return $this->belongsTo(FacebookAccount::class);
    }

    public function aiContents(): HasMany
    {
        return $this->hasMany(AiContent::class);
    }

    public function scheduledPosts(): HasMany
    {
        return $this->hasMany(ScheduledPost::class);
    }
}