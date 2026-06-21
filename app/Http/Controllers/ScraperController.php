<?php

namespace App\Http\Controllers;

use App\Jobs\ScrapeReelsJob;
use App\Models\FacebookAccount;
use App\Models\Video;
use App\Services\FacebookGraphService;
use App\Services\FacebookRawScraperService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ScraperController extends Controller
{
    public function __construct(
        protected FacebookGraphService $facebookGraph
    ) {}

    /**
     * Show scraper dashboard.
     */
    public function index()
    {
        $accounts = Auth::user()->facebookAccounts;
        $recentVideos = Video::where('user_id', Auth::id())->latest()->take(20)->get();

        return view('scraper.index', compact('accounts', 'recentVideos'));
    }

    /**
     * Fetch pages from a connected Facebook account.
     */
    public function fetchPages(FacebookAccount $account)
    {
        if ($account->user_id !== Auth::id()) {
            abort(403);
        }

        $pages = $this->facebookGraph->getUserPages($account);

        if (!empty($pages)) {
            $account->update(['pages' => $pages]);
            return response()->json([
                'success' => true,
                'pages' => $pages,
                'message' => 'Pages fetched successfully.',
            ]);
        }

        return response()->json([
            'success' => false,
            'pages' => [],
            'message' => 'No pages found for this account.',
        ]);
    }

    /**
     * Scrape reels from a Facebook page (using queue).
     */
    public function scrape(Request $request)
    {
        $data = $request->validate([
            'facebook_account_id' => ['required', 'exists:facebook_accounts,id'],
            'page_id' => ['required', 'string'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $account = FacebookAccount::findOrFail($data['facebook_account_id']);

        if ($account->user_id !== Auth::id()) {
            abort(403);
        }

        try {
            // Dispatch to queue
            ScrapeReelsJob::dispatchSync($account, $data['page_id'], null, $data['limit'] ?? 50);
            return redirect()->back()->with('success', 'Berhasil menyedot video Reels!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    /**
     * Search reels by keyword across all pages (using queue for each page).
     */
    public function searchByKeyword(Request $request)
    {
        $data = $request->validate([
            'facebook_account_id' => ['required', 'exists:facebook_accounts,id'],
            'keyword' => ['required', 'string', 'min:2', 'max:200'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $account = FacebookAccount::findOrFail($data['facebook_account_id']);

        if ($account->user_id !== Auth::id()) {
            abort(403);
        }

        try {
            $scraper = app(FacebookRawScraperService::class);
            // Limit safely to max 20 per request to avoid blocking
            $limit = min($data['limit'] ?? 10, 20);
            
            $savedCount = $scraper->searchGlobalVideos($data['keyword'], $limit, $account->id);

            return redirect()->back()->with('success', "Sukses! Berhasil menyedot {$savedCount} video publik untuk kata kunci '{$data['keyword']}'.");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', "Scraper Gagal: " . $e->getMessage());
        }
    }

    /**
     * Search reels by author/user ID.
     */
    public function searchByUser(Request $request)
    {
        $data = $request->validate([
            'facebook_account_id' => ['required', 'exists:facebook_accounts,id'],
            'user_id' => ['required', 'string', 'min:2', 'max:200'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $account = FacebookAccount::findOrFail($data['facebook_account_id']);

        if ($account->user_id !== Auth::id()) {
            abort(403);
        }

        $reels = $this->facebookGraph->searchReelsByUser(
            $account,
            $data['user_id'],
            $data['limit'] ?? 50
        );

        $saved = $this->facebookGraph->saveReels(
            $account,
            'user_search',
            $reels,
            'user',
            $data['user_id'],
            $data['user_id']
        );

        $message = "Found {$saved} new reel(s) from user \"{$data['user_id']}\".";

        return redirect()->back()->with('success', $message);
    }

    /**
     * List all scraped videos with search & filter.
     */
    public function listVideos(Request $request)
    {
        $query = Video::where('user_id', Auth::id())->with('facebookAccount');

        // Search / Filter parameters
        $search = $request->input('search');
        $sourceType = $request->input('source_type');
        $accountId = $request->input('facebook_account_id');

        if ($search || $sourceType || $accountId) {
            $query->search($search, $sourceType, $accountId);
        }

        $videos = $query->latest()->paginate(20)->withQueryString();

        $accounts = Auth::user()->facebookAccounts;

        return view('scraper.videos', compact('videos', 'accounts'));
    }

    /**
     * Delete a scraped video.
     */
    public function deleteVideo(Video $video)
    {
        if ($video->user_id !== Auth::id()) {
            abort(403);
        }

        $video->delete();

        return redirect()->back()->with('success', 'Video deleted successfully.');
    }
}