<?php

namespace App\Jobs;

use App\Models\FacebookAccount;
use App\Services\FacebookGraphService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class ScrapeReelsJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected FacebookAccount $account,
        protected string $pageId,
        protected ?string $keyword = null,
        protected int $limit = 50
    ) {}

    public function handle(FacebookGraphService $facebookGraph): void
    {
        try {
            $reels = $facebookGraph->scrapeReels($this->account, $this->pageId, $this->limit, $this->keyword);
            
            $saved = $facebookGraph->saveReels(
                $this->account,
                $this->pageId,
                $reels,
                $this->keyword ? 'keyword_search' : 'page',
                $this->keyword,
                $this->keyword
            );

            Log::info("ScrapeReelsJob completed: {$saved} reels saved for page {$this->pageId}");
        } catch (\Exception $e) {
            Log::error("ScrapeReelsJob failed for page {$this->pageId}: " . $e->getMessage());
            throw $e;
        }
    }
}