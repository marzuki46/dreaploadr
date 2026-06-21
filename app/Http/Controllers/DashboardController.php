<?php

namespace App\Http\Controllers;

use App\Models\FacebookAccount;
use App\Models\Video;
use App\Models\ScheduledPost;
use App\Models\AiContent;
use App\Models\Affiliate;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $stats = [
            'accounts_count' => FacebookAccount::where('user_id', $user->id)->count(),
            'videos_count' => Video::where('user_id', $user->id)->count(),
            'ai_contents_count' => AiContent::where('user_id', $user->id)->count(),
            'scheduled_posts_count' => ScheduledPost::where('user_id', $user->id)->count(),
            'published_posts_count' => ScheduledPost::where('user_id', $user->id)->where('status', 'published')->count(),
            'affiliate_earnings' => Affiliate::where('user_id', $user->id)->sum('total_commission'),
            'transactions_count' => Transaction::where('user_id', $user->id)->count(),
        ];

        $recentVideos = Video::where('user_id', $user->id)->latest()->take(8)->get();
        $upcomingPosts = ScheduledPost::where('user_id', $user->id)
            ->where('scheduled_time', '>', now())
            ->where('status', 'pending')
            ->orderBy('scheduled_time')
            ->take(5)
            ->get();
        $accounts = FacebookAccount::where('user_id', $user->id)->get();

        return view('dashboard.index', compact('stats', 'recentVideos', 'upcomingPosts', 'accounts'));
    }
}