<?php

namespace App\Services;

use App\Models\Setting;
use App\Models\Video;
use App\Models\FacebookAccount;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FacebookRawScraperService
{
    private array $userAgents = [
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/119.0.0.0 Safari/537.36',
        'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/118.0.0.0 Safari/537.36',
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36 Edg/120.0.0.0',
    ];

    /**
     * Scrape global Facebook video search using raw cookie.
     */
    public function searchGlobalVideos(string $keyword, int $limit = 10, ?int $facebookAccountId = null): int
    {
        $cookie = Setting::getVal('facebook_cookie', '');
        if (empty($cookie)) {
            throw new \Exception("Facebook Cookie belum diatur di General Settings.");
        }

        $userAgent = $this->userAgents[array_rand($this->userAgents)];

        // Search URL
        $url = "https://www.facebook.com/search/videos/?q=" . urlencode($keyword);

        $response = Http::withHeaders([
            'User-Agent' => $userAgent,
            'Cookie' => $cookie,
            'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8',
            'Accept-Language' => 'en-US,en;q=0.9',
            'Sec-Fetch-Dest' => 'document',
            'Sec-Fetch-Mode' => 'navigate',
            'Sec-Fetch-Site' => 'none',
            'Sec-Fetch-User' => '?1',
            'Upgrade-Insecure-Requests' => '1',
        ])->get($url);

        if (!$response->successful()) {
            Log::error('Raw Scraper HTTP Error', ['status' => $response->status()]);
            throw new \Exception("Gagal menghubungi server Facebook (Status: {$response->status()}). Mungkin diblokir.");
        }

        $html = $response->body();

        // Check if cookie is invalid (redirects to login)
        if (str_contains($html, 'id="login_form"') || str_contains($html, 'name="pass"')) {
            throw new \Exception("Cookie kedaluwarsa atau tidak valid. Facebook mengembalikan halaman login.");
        }

        $videos = $this->extractVideosFromHtml($html, $keyword, $limit);

        if (empty($videos)) {
            Log::warning('Raw Scraper No Videos Found', ['html_length' => strlen($html)]);
            throw new \Exception("Tidak ada video yang ditemukan dari HTML. Mungkin struktur Facebook berubah atau diblokir sementara.");
        }

        return $this->saveVideosToDb($videos, $facebookAccountId);
    }

    private function extractVideosFromHtml(string $html, string $keyword, int $limit): array
    {
        $videos = [];
        
        // Facebook embeds GraphQL state inside script tags. 
        // We will look for video objects.
        // This regex looks for typical video JSON objects.
        preg_match_all('/\{"__typename":"Video","id":"(\d+)".*?"playable_url(_quality_hd)?":"([^"]+)"/i', $html, $matches, PREG_SET_ORDER);

        $count = 0;
        foreach ($matches as $match) {
            if ($count >= $limit) break;

            $videoId = $match[1];
            $videoUrl = str_replace('\/', '/', $match[3]);

            // Try to extract text/description. It's usually nearby but hard to perfectly regex without a full JSON parser.
            // We will just use the keyword as a placeholder title if we can't find it easily.
            $title = "FB Video - " . ucfirst($keyword);
            
            // To find author name, we can do a localized search near the video ID.
            $author = "Unknown Author";
            
            $videos[] = [
                'facebook_video_id' => $videoId,
                'source_url' => $videoUrl,
                'thumbnail_url' => "https://graph.facebook.com/{$videoId}/picture", // Graph API still works for basic thumbnails often
                'title' => $title,
                'author_name' => $author,
                'description' => "Scraped via Keyword Search: " . $keyword,
                'keywords' => $keyword,
            ];
            $count++;
        }

        return $videos;
    }

    private function saveVideosToDb(array $videos, ?int $facebookAccountId): int
    {
        $saved = 0;
        $userId = auth()->id() ?? 1;

        foreach ($videos as $reel) {
            $exists = Video::where('facebook_video_id', $reel['facebook_video_id'])->exists();

            if (!$exists) {
                Video::create([
                    'user_id' => $userId,
                    'facebook_account_id' => $facebookAccountId,
                    'facebook_page_id' => null,
                    'facebook_video_id' => $reel['facebook_video_id'],
                    'source_url' => $reel['source_url'],
                    'thumbnail_url' => $reel['thumbnail_url'],
                    'title' => $reel['title'],
                    'author_name' => $reel['author_name'],
                    'description' => $reel['description'],
                    'keywords' => $reel['keywords'],
                    'source_type' => 'global_search',
                    'search_query' => $reel['keywords'],
                    'status' => 'scraped',
                ]);
                $saved++;
            }
        }

        return $saved;
    }
}
