<?php

namespace App\Services;

use App\Models\Setting;
use App\Models\Video;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FacebookRawScraperService
{
    private array $desktopAgents = [
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36',
        'Mozilla/5.0 (Macintosh; Intel Mac OS X 14_4_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36',
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:124.0) Gecko/20100101 Firefox/124.0',
    ];

    private array $mobileAgents = [
        'Mozilla/5.0 (Linux; Android 14; Pixel 8) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Mobile Safari/537.36',
        'Mozilla/5.0 (iPhone; CPU iPhone OS 17_4 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.4 Mobile/15E148 Safari/604.1',
        'Mozilla/5.0 (Linux; Android 13; SM-S918B) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Mobile Safari/537.36',
    ];

    /**
     * Master method: search global Facebook videos using raw cookie.
     * Tries multiple strategies until one works.
     */
    public function searchGlobalVideos(string $keyword, int $limit = 10, ?int $facebookAccountId = null): int
    {
        $cookie = Setting::getVal('facebook_cookie', '');
        if (empty($cookie)) {
            throw new \Exception("Facebook Cookie belum diatur di General Settings. Silakan isi di panel Super Admin -> General Settings.");
        }

        $videos = [];
        $errors = [];

        // Strategy 1: mbasic.facebook.com (paling ringan, jarang diblokir)
        try {
            Log::info("Scraper: Mencoba Strategy 1 (mbasic.facebook.com)");
            $videos = $this->scrapeMbasic($cookie, $keyword, $limit);
            if (!empty($videos)) {
                Log::info("Scraper: Strategy 1 BERHASIL, ditemukan " . count($videos) . " video.");
            }
        } catch (\Exception $e) {
            $errors[] = "Strategy 1 (mbasic): " . $e->getMessage();
            Log::warning("Scraper Strategy 1 Gagal: " . $e->getMessage());
        }

        // Strategy 2: m.facebook.com (mobile)
        if (empty($videos)) {
            try {
                Log::info("Scraper: Mencoba Strategy 2 (m.facebook.com)");
                $videos = $this->scrapeMobile($cookie, $keyword, $limit);
                if (!empty($videos)) {
                    Log::info("Scraper: Strategy 2 BERHASIL, ditemukan " . count($videos) . " video.");
                }
            } catch (\Exception $e) {
                $errors[] = "Strategy 2 (mobile): " . $e->getMessage();
                Log::warning("Scraper Strategy 2 Gagal: " . $e->getMessage());
            }
        }

        // Strategy 3: Desktop with GraphQL JSON extraction
        if (empty($videos)) {
            try {
                Log::info("Scraper: Mencoba Strategy 3 (Desktop GraphQL)");
                $videos = $this->scrapeDesktopGraphQL($cookie, $keyword, $limit);
                if (!empty($videos)) {
                    Log::info("Scraper: Strategy 3 BERHASIL, ditemukan " . count($videos) . " video.");
                }
            } catch (\Exception $e) {
                $errors[] = "Strategy 3 (Desktop): " . $e->getMessage();
                Log::warning("Scraper Strategy 3 Gagal: " . $e->getMessage());
            }
        }

        if (empty($videos)) {
            $errorSummary = implode(' | ', $errors);
            Log::error("Semua strategy scraper gagal: " . $errorSummary);
            throw new \Exception(
                "Semua metode scraping gagal. Kemungkinan: (1) Cookie Facebook sudah kedaluwarsa/tidak valid. " .
                "Silakan perbarui Cookie di General Settings. (2) IP server diblokir sementara oleh Facebook. " .
                "Coba lagi beberapa menit kemudian. Detail: " . $errors[0]
            );
        }

        return $this->saveVideosToDb($videos, $facebookAccountId);
    }

    /**
     * Strategy 1: Scrape mbasic.facebook.com - paling sederhana & jarang diblokir
     */
    private function scrapeMbasic(string $cookie, string $keyword, int $limit): array
    {
        $ua = $this->mobileAgents[array_rand($this->mobileAgents)];
        $url = "https://mbasic.facebook.com/search/videos/?q=" . urlencode($keyword);

        $response = Http::withOptions(['verify' => false, 'timeout' => 30])
            ->withHeaders([
                'User-Agent'      => $ua,
                'Cookie'          => $cookie,
                'Accept'          => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                'Accept-Language' => 'en-US,en;q=0.5',
                'Referer'         => 'https://mbasic.facebook.com/',
                'Connection'      => 'keep-alive',
            ])->get($url);

        if (!$response->successful()) {
            throw new \Exception("HTTP Error: " . $response->status());
        }

        $html = $response->body();

        if ($this->isLoginPage($html)) {
            throw new \Exception("Cookie kedaluwarsa - Facebook meminta login ulang.");
        }

        return $this->extractFromMbasicHtml($html, $keyword, $limit);
    }

    /**
     * Strategy 2: Mobile Facebook
     */
    private function scrapeMobile(string $cookie, string $keyword, int $limit): array
    {
        $ua = $this->mobileAgents[array_rand($this->mobileAgents)];
        $url = "https://m.facebook.com/search/videos/?q=" . urlencode($keyword);

        $response = Http::withOptions(['verify' => false, 'timeout' => 30])
            ->withHeaders([
                'User-Agent'      => $ua,
                'Cookie'          => $cookie,
                'Accept'          => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                'Accept-Language' => 'en-US,en;q=0.5',
                'Referer'         => 'https://m.facebook.com/',
            ])->get($url);

        if (!$response->successful()) {
            throw new \Exception("HTTP Error: " . $response->status());
        }

        $html = $response->body();

        if ($this->isLoginPage($html)) {
            throw new \Exception("Cookie kedaluwarsa - Facebook meminta login ulang.");
        }

        // Extract video IDs from mobile HTML
        return $this->extractVideoIds($html, $keyword, $limit);
    }

    /**
     * Strategy 3: Desktop Facebook - extract embedded JSON GraphQL data
     */
    private function scrapeDesktopGraphQL(string $cookie, string $keyword, int $limit): array
    {
        $ua = $this->desktopAgents[array_rand($this->desktopAgents)];
        $url = "https://www.facebook.com/search/videos/?q=" . urlencode($keyword);

        $response = Http::withOptions(['verify' => false, 'timeout' => 30])
            ->withHeaders([
                'User-Agent'               => $ua,
                'Cookie'                   => $cookie,
                'Accept'                   => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8',
                'Accept-Language'          => 'en-US,en;q=0.9',
                'Sec-Fetch-Dest'           => 'document',
                'Sec-Fetch-Mode'           => 'navigate',
                'Sec-Fetch-Site'           => 'none',
                'Sec-Fetch-User'           => '?1',
                'Upgrade-Insecure-Requests'=> '1',
            ])->get($url);

        if (!$response->successful()) {
            throw new \Exception("HTTP Error: " . $response->status());
        }

        $html = $response->body();

        if ($this->isLoginPage($html)) {
            throw new \Exception("Cookie kedaluwarsa - Facebook meminta login ulang.");
        }

        return $this->extractFromDesktopHtml($html, $keyword, $limit);
    }

    /**
     * Check if the response is a login page.
     */
    private function isLoginPage(string $html): bool
    {
        return str_contains($html, 'id="login_form"')
            || str_contains($html, 'name="pass"')
            || str_contains($html, '/login/?next=')
            || str_contains($html, '"login_page"');
    }

    /**
     * Extract videos from mbasic HTML.
     */
    private function extractFromMbasicHtml(string $html, string $keyword, int $limit): array
    {
        $videos = [];

        // mbasic has simpler structure with direct video links
        // Pattern: /video.php?v=VIDEOID or /videos/VIDEOID
        preg_match_all('/(?:video\.php\?v=|\/videos\/)(\d{10,20})/', $html, $idMatches);

        $ids = array_unique($idMatches[1] ?? []);

        foreach (array_slice($ids, 0, $limit) as $videoId) {
            $videos[] = $this->buildVideoData($videoId, $keyword);
        }

        // Also try to find thumbnail URLs
        preg_match_all('/src="(https:\/\/[^"]*scontent[^"]*\.(jpg|jpeg|png))"/', $html, $thumbMatches);
        $thumbs = $thumbMatches[1] ?? [];

        foreach ($videos as $i => &$video) {
            if (isset($thumbs[$i])) {
                $video['thumbnail_url'] = html_entity_decode($thumbs[$i]);
            }
        }

        return $videos;
    }

    /**
     * Extract video IDs from any Facebook HTML.
     */
    private function extractVideoIds(string $html, string $keyword, int $limit): array
    {
        $videos = [];
        $ids = [];

        // Various patterns for video IDs
        $patterns = [
            '/\/videos\/(\d{10,20})/',
            '/video\.php\?v=(\d{10,20})/',
            '/"videoId":"(\d{10,20})"/',
            '/"video_id":"(\d{10,20})"/',
            '/\/reel\/(\d{10,20})/',
        ];

        foreach ($patterns as $pattern) {
            preg_match_all($pattern, $html, $matches);
            $ids = array_merge($ids, $matches[1] ?? []);
        }

        $ids = array_unique($ids);

        foreach (array_slice($ids, 0, $limit) as $videoId) {
            $videos[] = $this->buildVideoData($videoId, $keyword);
        }

        return $videos;
    }

    /**
     * Extract from desktop HTML with GraphQL JSON parsing.
     */
    private function extractFromDesktopHtml(string $html, string $keyword, int $limit): array
    {
        $videos = [];

        // Strategy: find embedded JSON with video data
        // Facebook puts data in require(['ScheduledServerJS']... blocks
        preg_match_all('/"id":"(\d{10,20})","__typename":"Video"/', $html, $m1);
        preg_match_all('/"__typename":"Video","id":"(\d{10,20})"/', $html, $m2);
        preg_match_all('/"video_id":"(\d{10,20})"/', $html, $m3);

        $ids = array_unique(array_merge(
            $m1[1] ?? [],
            $m2[1] ?? [],
            $m3[1] ?? []
        ));

        // Also extract playable URLs
        preg_match_all('/"playable_url(?:_quality_hd)?":"(https:\\\\u002F\\\\u002F[^"]+\.mp4[^"]*)"/', $html, $urlMatches);
        $urls = array_map(fn($u) => json_decode('"' . $u . '"'), $urlMatches[1] ?? []);

        foreach (array_slice($ids, 0, $limit) as $i => $videoId) {
            $data = $this->buildVideoData($videoId, $keyword);
            if (isset($urls[$i])) {
                $data['source_url'] = $urls[$i];
            }
            $videos[] = $data;
        }

        return $videos;
    }

    /**
     * Build a standard video data array from a video ID.
     */
    private function buildVideoData(string $videoId, string $keyword): array
    {
        return [
            'facebook_video_id' => $videoId,
            'source_url'        => "https://www.facebook.com/watch/?v={$videoId}",
            'thumbnail_url'     => "https://graph.facebook.com/{$videoId}/picture",
            'title'             => "Video - " . ucfirst($keyword),
            'author_name'       => 'Facebook User',
            'description'       => "Scraped via Keyword: {$keyword}",
            'keywords'          => $keyword,
        ];
    }

    /**
     * Save videos to DB.
     */
    private function saveVideosToDb(array $videos, ?int $facebookAccountId): int
    {
        $saved  = 0;
        $userId = auth()->id() ?? 1;

        foreach ($videos as $reel) {
            $exists = Video::where('facebook_video_id', $reel['facebook_video_id'])->exists();

            if (!$exists) {
                Video::create([
                    'user_id'            => $userId,
                    'facebook_account_id'=> $facebookAccountId,
                    'facebook_page_id'   => null,
                    'facebook_video_id'  => $reel['facebook_video_id'],
                    'source_url'         => $reel['source_url'],
                    'thumbnail_url'      => $reel['thumbnail_url'],
                    'title'              => $reel['title'],
                    'author_name'        => $reel['author_name'],
                    'description'        => $reel['description'],
                    'keywords'           => $reel['keywords'],
                    'source_type'        => 'global_search',
                    'search_query'       => $reel['keywords'],
                    'status'             => 'scraped',
                ]);
                $saved++;
            }
        }

        return $saved;
    }
}
