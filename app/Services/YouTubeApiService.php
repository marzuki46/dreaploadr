<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class YouTubeApiService
{
    /**
     * Post a video to YouTube.
     */
    public function postVideo(User $user, string $videoUrl, string $title, string $description): ?string
    {
        if (!$user->youtube_access_token) {
            Log::error("YouTube API: No access token for user {$user->id}");
            return null;
        }

        // 1. Download the video to a temporary file
        $tempVideoPath = storage_path('app/temp_yt_' . Str::random(10) . '.mp4');
        try {
            $videoData = file_get_contents($videoUrl);
            if (!$videoData) {
                Log::error("YouTube API: Failed to download video from {$videoUrl}");
                return null;
            }
            file_put_contents($tempVideoPath, $videoData);

            // 2. Refresh token if necessary (simplified, assumes token is valid or refreshed elsewhere)
            // In a complete implementation, we should check if token is expired and use youtube_refresh_token.

            // 3. Upload to YouTube
            $metadata = [
                'snippet' => [
                    'title' => substr($title, 0, 100),
                    'description' => substr($description, 0, 5000),
                    'tags' => ['shorts', 'reels', 'viral'],
                    'categoryId' => '22', // People & Blogs
                ],
                'status' => [
                    'privacyStatus' => 'public',
                    'selfDeclaredMadeForKids' => false,
                ]
            ];

            $response = Http::withToken($user->youtube_access_token)
                ->attach('video', file_get_contents($tempVideoPath), 'video.mp4')
                ->post('https://www.googleapis.com/upload/youtube/v3/videos?uploadType=multipart&part=snippet,status', [
                    'metadata' => json_encode($metadata)
                ]);

            if ($response->successful()) {
                return $response->json('id');
            }

            Log::error('YouTube post video failed', [
                'error' => $response->json(),
                'user_id' => $user->id,
            ]);

            return null;

        } catch (\Exception $e) {
            Log::error('YouTube post video exception', [
                'message' => $e->getMessage(),
                'user_id' => $user->id,
            ]);
            return null;
        } finally {
            if (file_exists($tempVideoPath)) {
                unlink($tempVideoPath);
            }
        }
    }
}
