<?php

namespace App\Http\Controllers;

use App\Models\ScheduledPost;
use App\Models\Video;
use App\Models\FacebookAccount;
use App\Services\FacebookGraphService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ScheduledPostController extends Controller
{
    public function __construct(
        protected FacebookGraphService $facebookGraph
    ) {}

    /**
     * Show scheduled posts.
     */
    public function index()
    {
        $videos = Video::where('user_id', Auth::id())->latest()->get();
        $accounts = FacebookAccount::where('user_id', Auth::id())->get();
        $user = Auth::user();
        $scheduledPosts = ScheduledPost::where('user_id', Auth::id())
            ->with(['video', 'facebookAccount'])
            ->latest()
            ->paginate(15);

        return view('scheduled-posts.index', compact('videos', 'accounts', 'scheduledPosts', 'user'));
    }

    /**
     * Create a scheduled post (multi-platform).
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'video_id' => ['required', 'exists:videos,id'],
            'platform' => ['required', 'string', 'in:facebook,youtube,tiktok'],
            'facebook_account_id' => ['required_if:platform,facebook', 'nullable', 'exists:facebook_accounts,id'],
            'facebook_page_id' => ['required_if:platform,facebook', 'nullable', 'string'],
            'platform_page_id' => ['nullable', 'string'],
            'description' => ['nullable', 'string', 'max:500'],
            'scheduled_time' => ['required', 'date', 'after:now'],
        ]);

        $video = Video::findOrFail($data['video_id']);
        if ($video->user_id !== Auth::id()) {
            abort(403);
        }

        // Validate FB account ownership
        if ($data['platform'] === 'facebook') {
            $account = FacebookAccount::findOrFail($data['facebook_account_id']);
            if ($account->user_id !== Auth::id()) {
                abort(403);
            }
        }

        // Check platform permission
        $user = Auth::user();
        if ($data['platform'] === 'youtube' && !$user->canPostToYouTube()) {
            return back()->withErrors(['platform' => 'You do not have YouTube posting enabled.']);
        }
        if ($data['platform'] === 'tiktok' && !$user->canPostToTikTok()) {
            return back()->withErrors(['platform' => 'You do not have TikTok posting enabled.']);
        }

        ScheduledPost::create([
            'user_id' => Auth::id(),
            'platform' => $data['platform'],
            'video_id' => $data['video_id'],
            'facebook_account_id' => $data['facebook_account_id'] ?? null,
            'facebook_page_id' => $data['facebook_page_id'] ?? null,
            'platform_page_id' => $data['platform_page_id'] ?? null,
            'description' => $data['description'] ?? $video->title,
            'scheduled_time' => $data['scheduled_time'],
            'status' => 'scheduled',
        ]);

        return back()->with('success', 'Post scheduled successfully for ' . ucfirst($data['platform']) . '.');
    }

    /**
     * Update scheduled post.
     */
    public function update(Request $request, ScheduledPost $scheduledPost)
    {
        if ($scheduledPost->user_id !== Auth::id()) {
            abort(403);
        }

        $data = $request->validate([
            'description' => ['nullable', 'string', 'max:500'],
            'scheduled_time' => ['nullable', 'date', 'after:now'],
            'status' => ['nullable', 'string', 'in:pending,published,failed'],
        ]);

        $scheduledPost->update($data);

        return back()->with('success', 'Scheduled post updated.');
    }

    /**
     * Delete scheduled post.
     */
    public function destroy(ScheduledPost $scheduledPost)
    {
        if ($scheduledPost->user_id !== Auth::id()) {
            abort(403);
        }

        $scheduledPost->delete();

        return back()->with('success', 'Scheduled post deleted.');
    }
}