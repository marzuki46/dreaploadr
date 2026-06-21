<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasName;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
class User extends Authenticatable implements FilamentUser, HasName
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'subscription_plan',
        'bank_account_number',
        'google_id',
        'facebook_id',
        'avatar',
        'facebook_access_token',
        'subscription_ends_at',
        'is_onboarding_completed',
        'can_post_youtube',
        'can_post_tiktok',
        'youtube_access_token',
        'youtube_refresh_token',
        'youtube_token_expires_at',
        'tiktok_access_token',
        'tiktok_refresh_token',
        'tiktok_token_expires_at',
        'facebook_token_expires_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'facebook_access_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'subscription_ends_at' => 'datetime',
            'is_onboarding_completed' => 'boolean',
            'can_post_youtube' => 'boolean',
            'can_post_tiktok' => 'boolean',
            'youtube_token_expires_at' => 'datetime',
            'tiktok_token_expires_at' => 'datetime',
            'facebook_token_expires_at' => 'datetime',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() === 'super-admin') {
            return $this->role === 'super_admin';
        }

        return true;
    }

    public function getFilamentName(): string
    {
        return $this->name;
    }

    public function videos(): HasMany
    {
        return $this->hasMany(Video::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function facebookAccounts(): HasMany
    {
        return $this->hasMany(FacebookAccount::class);
    }

    public function affiliates(): HasMany
    {
        return $this->hasMany(Affiliate::class);
    }

    public function hasActiveSubscription(): bool
    {
        return $this->subscription_ends_at && $this->subscription_ends_at->isFuture();
    }

    public function canPostToYouTube(): bool
    {
        return $this->can_post_youtube && $this->hasActiveSubscription();
    }

    public function canPostToTikTok(): bool
    {
        return $this->can_post_tiktok && $this->hasActiveSubscription();
    }

    public function availablePlatforms(): array
    {
        $platforms = [];
        if (\App\Models\Setting::getVal('enable_facebook', true)) {
            $platforms[] = 'facebook';
        }
        if (\App\Models\Setting::getVal('enable_youtube', true) && $this->canPostToYouTube()) {
            $platforms[] = 'youtube';
        }
        if (\App\Models\Setting::getVal('enable_tiktok', true) && $this->canPostToTikTok()) {
            $platforms[] = 'tiktok';
        }
        return $platforms;
    }
}
