<?php

use App\Console\Commands\PublishScheduledPosts;
use Illuminate\Support\Facades\Schedule;

// Publish scheduled posts every minute
Schedule::command(PublishScheduledPosts::class)->everyMinute();

// Refresh OAuth tokens hourly
Schedule::job(new \App\Jobs\RefreshOAuthTokensJob())->hourly();