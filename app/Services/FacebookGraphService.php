<?php

namespace App\Services;

use App\Models\FacebookAccount;
use App\Models\Video;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FacebookGraphService
{
    private string $baseUrl = 'https://graph.facebook.com/v21.0';

    /**
     * Get user's Facebook pages.
     */
    public function getUserPages(FacebookAccount $account): array
    {
        $response = Http::get("{$this->baseUrl}/me/accounts", [
            'access_token' => $account->access_token,
            'fields' => 'id,name,access_token,category,fan_count,picture',
        ]);

        if ($response->successful()) {
            return $response->json()['data'] ?? [];
        }

        Log::error('Facebook get pages failed', [
            'error' => $response->json(),
            'account_id' => $account->id,
        ]);

        return [];
    }

    /**
     * Search public pages globally by keyword.
     * Note: Requires 'Page Public Content Access' permission for live apps.
     */
    public function searchPublicPages(FacebookAccount $account, string $keyword, int $limit = 5): array
    {
        $response = Http::get("{$this->baseUrl}/pages/search", [
            'access_token' => $account->access_token,
            'q' => $keyword,
            'fields' => 'id,name',
            'limit' => $limit,
        ]);

        if ($response->successful()) {
            return $response->json()['data'] ?? [];
        }

        Log::error('Facebook pages search failed', [
            'error' => $response->json(),
            'keyword' => $keyword,
        ]);

        return [];
    }

    /**
     * Scrape reels from a Facebook page.
     */
    public function scrapeReels(FacebookAccount $account, string $pageId, int $limit = 50, ?string $keyword = null): array
    {
        $reels = [];

        // Get page access token
        $pageToken = $this->getPageAccessToken($account, $pageId);
        if (!$pageToken) {
            return $reels;
        }

        $response = Http::get("{$this->baseUrl}/{$pageId}/video_reels", [
            'access_token' => $pageToken,
            'fields' => 'id,description,media_type,source,thumbnail_url,permalink,created_time,length,from',
            'limit' => min($limit, 100),
        ]);

        if ($response->successful()) {
            $data = $response->json()['data'] ?? [];

            foreach ($data as $reel) {
                $desc = $reel['description'] ?? '';

                // Jika ada keyword filter, skip yg tidak match
                if ($keyword && !str_contains(strtolower($desc), strtolower($keyword))) {
                    continue;
                }

                $from = $reel['from'] ?? null;
                $authorName = $from['name'] ?? null;

                $reels[] = [
                    'facebook_video_id' => $reel['id'],
                    'source_url' => $reel['source'] ?? null,
                    'thumbnail_url' => $reel['thumbnail_url'] ?? null,
                    'title' => $desc ?: 'Untitled',
                    'author_name' => $authorName,
                    'description' => substr($desc, 0, 500),
                    'permalink' => $reel['permalink'] ?? null,
                    'duration' => $reel['length'] ?? null,
                    'created_at_reel' => $reel['created_time'] ?? null,
                ];
            }
        } else {
            Log::error('Facebook scrape reels failed', [
                'error' => $response->json(),
                'page_id' => $pageId,
            ]);
        }

        return $reels;
    }

    /**
     * Search reels by keyword across all pages of an account.
     */
    public function searchReelsByKeyword(FacebookAccount $account, string $keyword, int $limit = 50): array
    {
        $pages = $account->pages ?? [];
        if (empty($pages)) {
            $pages = $this->getUserPages($account);
        }

        $allReels = [];
        foreach ($pages as $page) {
            $pageId = $page['id'] ?? '';
            if (!$pageId) continue;

            $reels = $this->scrapeReels($account, $pageId, $limit, $keyword);

            foreach ($reels as &$reel) {
                $reel['keywords'] = $keyword;
            }
            unset($reel);

            $allReels = array_merge($allReels, $reels);
            if (count($allReels) >= $limit) break;
        }

        return array_slice($allReels, 0, $limit);
    }

    /**
     * Search reels by author name/user ID across all pages.
     */
    public function searchReelsByUser(FacebookAccount $account, string $userId, int $limit = 50): array
    {
        $pages = $account->pages ?? [];
        if (empty($pages)) {
            $pages = $this->getUserPages($account);
        }

        $allReels = [];
        foreach ($pages as $page) {
            $pageId = $page['id'] ?? '';
            if (!$pageId) continue;

            $reels = $this->scrapeReels($account, $pageId, $limit * 2);

            foreach ($reels as $reel) {
                if (isset($reel['author_name']) && str_contains(strtolower($reel['author_name']), strtolower($userId))) {
                    $reel['search_query'] = $userId;
                    $reel['keywords'] = $userId;
                    $allReels[] = $reel;
                }
                if (count($allReels) >= $limit) break;
            }

            if (count($allReels) >= $limit) break;
        }

        return array_slice($allReels, 0, $limit);
    }

    /**
     * Post a video to Facebook page.
     */
    public function postReel(FacebookAccount $account, string $pageId, string $videoUrl, string $description = ''): ?string
    {
        $pageToken = $this->getPageAccessToken($account, $pageId);
        if (!$pageToken) {
            return null;
        }

        $response = Http::post("{$this->baseUrl}/{$pageId}/video_reels", [
            'access_token' => $pageToken,
            'source_url' => $videoUrl,
            'description' => $description,
        ]);

        if ($response->successful()) {
            return $response->json()['id'] ?? null;
        }

        Log::error('Facebook post reel failed', [
            'error' => $response->json(),
            'page_id' => $pageId,
        ]);

        return null;
    }

    /**
     * Get page-specific access token.
     */
    private function getPageAccessToken(FacebookAccount $account, string $pageId): ?string
    {
        $pages = $account->pages ?? [];

        foreach ($pages as $page) {
            if (($page['id'] ?? '') === $pageId) {
                return $page['access_token'] ?? $account->access_token;
            }
        }

        // Fallback: try to get fresh token
        $freshPages = $this->getUserPages($account);
        foreach ($freshPages as $page) {
            if (($page['id'] ?? '') === $pageId) {
                return $page['access_token'] ?? $account->access_token;
            }
        }

        return $account->access_token;
    }

    /**
     * Refresh the access token using the Facebook Graph API.
     */
    public function refreshToken(FacebookAccount $account): ?string
    {
        return $account->access_token; // long-lived token lasts 60 days
    }

    /**
     * Save scraped reels to database.
     */
    public function saveReels(FacebookAccount $account, string $pageId, array $reels, string $sourceType = 'page', ?string $searchQuery = null, ?string $keyword = null): int
    {
        $saved = 0;

        foreach ($reels as $reel) {
            $exists = Video::where('facebook_video_id', $reel['facebook_video_id'])->exists();

            if (!$exists) {
                Video::create([
                    'user_id' => $account->user_id,
                    'facebook_account_id' => $account->id,
                    'facebook_page_id' => $pageId,
                    'facebook_video_id' => $reel['facebook_video_id'],
                    'source_url' => $reel['source_url'],
                    'thumbnail_url' => $reel['thumbnail_url'],
                    'title' => $reel['title'],
                    'author_name' => $reel['author_name'] ?? null,
                    'description' => $reel['description'],
                    'keywords' => $keyword ?? ($reel['keywords'] ?? null),
                    'source_type' => $sourceType,
                    'search_query' => $searchQuery ?? ($reel['search_query'] ?? null),
                    'status' => 'scraped',
                ]);
                $saved++;
            }
        }

        return $saved;
    }
}