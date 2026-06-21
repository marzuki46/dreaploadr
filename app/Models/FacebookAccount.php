<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FacebookAccount extends Model
{
    protected $fillable = [
        'user_id',
        'facebook_user_id',
        'name',
        'email',
        'access_token',
        'refresh_token',
        'pages',
        'token_expires_at',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'pages' => 'json',
            'token_expires_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function videos(): HasMany
    {
        return $this->hasMany(Video::class, 'facebook_account_id');
    }
}