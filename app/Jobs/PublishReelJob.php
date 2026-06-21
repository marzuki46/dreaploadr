<?php

namespace App\Jobs;

use App\Models\FacebookAccount;
use App\Models\ScheduledPost;
use App\Services\FacebookGraphService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class PublishReelJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected ScheduledPost $scheduledPost
    ) {}

    public function handle(FacebookGraphService $facebookGraph): void
    {
        try {
            $post = $this->scheduledPost;
            $platform = $post->platform ?? 'facebook';
            $user = $post->user;

            $video = $post->video;
            if (!$video || !$video->source_url) {
                $post->update([
                    'status' => 'failed',
                    'error_message' => 'Video or source URL not found.',
                ]);
                Log::error("PublishReelJob failed: video or source_url not found for post {$post->id}");
                return;
            }

            $result = null;

            if ($platform === 'facebook') {
                $account = $post->facebookAccount;
                if (!$account) {
                    $post->update(['status' => 'failed', 'error_message' => 'Facebook account not found.']);
                    return;
                }
                $result = $facebookGraph->postReel($account, $post->facebook_page_id, $video->source_url, $post->description ?? $video->title);
            } elseif ($platform === 'youtube') {
                $youtubeApi = app(\App\Services\YouTubeApiService::class);
                $result = $youtubeApi->postVideo($user, $video->source_url, $video->title, $post->description ?? $video->title);
            } elseif ($platform === 'tiktok') {
                $tiktokApi = app(\App\Services\TikTokApiService::class);
                $result = $tiktokApi->postVideo($user, $video->source_url, $video->title, $post->description ?? $video->title);
            } else {
                $post->update(['status' => 'failed', 'error_message' => "Publishing to {$platform} is not supported."]);
                return;
            }

            if ($result) {
                $post->update([
                    'status' => 'published',
                    'published_at' => now(),
                    'platform_post_id' => $result,
                    'error_message' => null,
                ]);
                Log::info("PublishReelJob completed: post {$post->id} published to {$platform} with ID {$result}");
            } else {
                $post->update([
                    'status' => 'failed',
                    'error_message' => "{$platform} API returned no post ID or failed.",
                ]);
                Log::error("PublishReelJob failed: {$platform} API returned no ID for post {$post->id}");
            }
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            Log::error("PublishReelJob failed for post {$this->scheduledPost->id}: {$msg}");
            $this->scheduledPost->update([
                'status' => 'failed',
                'error_message' => substr($msg, 0, 500),
            ]);
            throw $e;
        }
    }
}
