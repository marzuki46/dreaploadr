<?php

namespace App\Console\Commands;

use App\Jobs\PublishReelJob;
use App\Models\ScheduledPost;
use Illuminate\Console\Command;

class PublishScheduledPosts extends Command
{
    protected $signature = 'posts:publish';
    protected $description = 'Publish scheduled posts that are due';

    public function handle(): int
    {
        $duePosts = ScheduledPost::where('status', 'scheduled')
            ->where('scheduled_time', '<=', now())
            ->get();

        if ($duePosts->isEmpty()) {
            $this->info('No posts due for publishing.');
            return Command::SUCCESS;
        }

        $this->info("Found {$duePosts->count()} post(s) to publish.");

        foreach ($duePosts as $post) {
            PublishReelJob::dispatch($post);
            $this->line("Dispatched: Post #{$post->id}");
        }

        $this->info('All due posts have been dispatched to the queue.');

        return Command::SUCCESS;
    }
}