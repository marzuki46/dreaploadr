<?php

namespace App\Jobs;

use App\Models\Setting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RefreshOAuthTokensJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('Starting RefreshOAuthTokensJob...');

        // Refresh tokens that will expire in the next 48 hours
        $threshold = Carbon::now()->addHours(48);

        $usersToRefresh = User::where(function ($query) use ($threshold) {
            $query->whereNotNull('youtube_refresh_token')
                  ->where('youtube_token_expires_at', '<=', $threshold);
        })->orWhere(function ($query) use ($threshold) {
            $query->whereNotNull('tiktok_refresh_token')
                  ->where('tiktok_token_expires_at', '<=', $threshold);
        })->orWhere(function ($query) use ($threshold) {
            $query->whereNotNull('facebook_access_token')
                  ->where('facebook_token_expires_at', '<=', $threshold);
        })->get();

        foreach ($usersToRefresh as $user) {
            if ($user->youtube_refresh_token && $user->youtube_token_expires_at && $user->youtube_token_expires_at <= $threshold) {
                $this->refreshYouTubeToken($user);
            }

            if ($user->tiktok_refresh_token && $user->tiktok_token_expires_at && $user->tiktok_token_expires_at <= $threshold) {
                $this->refreshTikTokToken($user);
            }

            // Facebook usually issues long-lived tokens (60 days).
            if ($user->facebook_access_token && $user->facebook_token_expires_at && $user->facebook_token_expires_at <= $threshold) {
                $this->refreshFacebookToken($user);
            }
        }

        Log::info('Finished RefreshOAuthTokensJob.');
    }

    private function refreshYouTubeToken(User $user)
    {
        try {
            $clientId = Setting::getVal('youtube_client_id');
            $clientSecret = Setting::getVal('youtube_client_secret');

            if (!$clientId || !$clientSecret) {
                Log::warning("YouTube API credentials not configured in settings. Skipping refresh for user {$user->id}");
                return;
            }

            $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'refresh_token' => $user->youtube_refresh_token,
                'grant_type' => 'refresh_token',
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $user->youtube_access_token = $data['access_token'];
                // Google token usually expires in 3600 seconds
                $user->youtube_token_expires_at = Carbon::now()->addSeconds($data['expires_in'] ?? 3600);
                
                // Some OAuth providers return a new refresh token
                if (isset($data['refresh_token'])) {
                    $user->youtube_refresh_token = $data['refresh_token'];
                }
                
                $user->save();
                Log::info("Successfully refreshed YouTube token for user {$user->id}");
            } else {
                Log::error("Failed to refresh YouTube token for user {$user->id}: " . $response->body());
            }
        } catch (\Exception $e) {
            Log::error("Exception refreshing YouTube token for user {$user->id}: " . $e->getMessage());
        }
    }

    private function refreshTikTokToken(User $user)
    {
        try {
            $clientKey = Setting::getVal('tiktok_client_key');
            $clientSecret = Setting::getVal('tiktok_client_secret');

            if (!$clientKey || !$clientSecret) {
                Log::warning("TikTok API credentials not configured in settings. Skipping refresh for user {$user->id}");
                return;
            }

            $response = Http::asForm()->post('https://open.tiktokapis.com/v2/oauth/token/', [
                'client_key' => $clientKey,
                'client_secret' => $clientSecret,
                'refresh_token' => $user->tiktok_refresh_token,
                'grant_type' => 'refresh_token',
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $user->tiktok_access_token = $data['access_token'];
                $user->tiktok_token_expires_at = Carbon::now()->addSeconds($data['expires_in'] ?? 86400);
                
                if (isset($data['refresh_token'])) {
                    $user->tiktok_refresh_token = $data['refresh_token'];
                }
                
                $user->save();
                Log::info("Successfully refreshed TikTok token for user {$user->id}");
            } else {
                Log::error("Failed to refresh TikTok token for user {$user->id}: " . $response->body());
            }
        } catch (\Exception $e) {
            Log::error("Exception refreshing TikTok token for user {$user->id}: " . $e->getMessage());
        }
    }

    private function refreshFacebookToken(User $user)
    {
        try {
            $clientId = Setting::getVal('facebook_client_id');
            $clientSecret = Setting::getVal('facebook_client_secret');

            if (!$clientId || !$clientSecret) {
                Log::warning("Facebook API credentials not configured in settings. Skipping refresh for user {$user->id}");
                return;
            }

            $response = Http::get('https://graph.facebook.com/v19.0/oauth/access_token', [
                'grant_type' => 'fb_exchange_token',
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'fb_exchange_token' => $user->facebook_access_token,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $user->facebook_access_token = $data['access_token'];
                // Facebook long-lived tokens expire in ~60 days
                $user->facebook_token_expires_at = isset($data['expires_in']) 
                    ? Carbon::now()->addSeconds($data['expires_in']) 
                    : Carbon::now()->addDays(60);
                    
                $user->save();
                Log::info("Successfully refreshed Facebook token for user {$user->id}");
            } else {
                Log::error("Failed to refresh Facebook token for user {$user->id}: " . $response->body());
            }
        } catch (\Exception $e) {
            Log::error("Exception refreshing Facebook token for user {$user->id}: " . $e->getMessage());
        }
    }
}
