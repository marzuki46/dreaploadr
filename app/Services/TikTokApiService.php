<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class TikTokApiService
{
    /**
     * Post a video to TikTok.
     * Note: TikTok Content Posting API requires a multi-step upload process.
     */
    public function postVideo(User $user, string $videoUrl, string $title, string $description): ?string
    {
        if (!$user->tiktok_access_token) {
            Log::error("TikTok API: No access token for user {$user->id}");
            return null;
        }

        $tempVideoPath = storage_path('app/temp_tiktok_' . Str::random(10) . '.mp4');
        try {
            // 1. Download the video to a temporary file
            $videoData = file_get_contents($videoUrl);
            if (!$videoData) {
                Log::error("TikTok API: Failed to download video from {$videoUrl}");
                return null;
            }
            file_put_contents($tempVideoPath, $videoData);
            $videoSize = filesize($tempVideoPath);

            // 2. Initialize Video Upload
            $initResponse = Http::withToken($user->tiktok_access_token)
                ->post('https://open.tiktokapis.com/v2/post/publish/video/init/', [
                    'post_info' => [
                        'title' => substr($description ?: $title, 0, 150),
                        'privacy_level' => 'PUBLIC_TO_EVERYONE',
                        'disable_duet' => false,
                        'disable_comment' => false,
                        'disable_stitch' => false,
                        'video_cover_timestamp_ms' => 1000
                    ],
                    'source_info' => [
                        'source' => 'FILE_UPLOAD',
                        'video_size' => $videoSize,
                        'chunk_size' => $videoSize, // Uploading in one chunk for simplicity
                        'total_chunk_count' => 1
                    ]
                ]);

            if (!$initResponse->successful()) {
                Log::error('TikTok init upload failed', ['error' => $initResponse->json(), 'user_id' => $user->id]);
                return null;
            }

            $initData = $initResponse->json('data');
            $uploadUrl = $initData['upload_url'] ?? null;
            $publishId = $initData['publish_id'] ?? null;

            if (!$uploadUrl || !$publishId) {
                Log::error('TikTok init upload missing upload_url or publish_id', ['response' => $initData]);
                return null;
            }

            // 3. Upload the Video file
            // TikTok uses PUT request with the raw video data
            $uploadResponse = Http::withHeaders([
                'Content-Range' => 'bytes 0-' . ($videoSize - 1) . '/' . $videoSize,
                'Content-Type' => 'video/mp4'
            ])->withBody(file_get_contents($tempVideoPath), 'video/mp4')
              ->put($uploadUrl);

            // The response of the chunk upload does not return standard JSON usually, it just confirms chunk upload
            if (!$uploadResponse->successful()) {
                 Log::error('TikTok video upload failed', ['error' => $uploadResponse->body(), 'user_id' => $user->id]);
                 return null;
            }

            // After uploading all chunks (1 chunk here), TikTok processes it and publish_id is our reference
            return $publishId;

        } catch (\Exception $e) {
            Log::error('TikTok post video exception', [
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
