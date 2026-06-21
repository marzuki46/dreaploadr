@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-8">
        <h1 class="text-3xl font-bold">Dashboard</h1>
        <a href="/scraper" class="px-6 py-3 gradient-bg text-white font-semibold rounded-xl hover:opacity-90 transition shadow-lg shadow-indigo-200">
            Scrape Reels
        </a>
    </div>

    {{-- Stats Grid --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 24 24"><path d="M9.101 23.691v-7.98H6.627v-3.667h2.474v-1.58c0-4.085 1.848-5.978 5.858-5.978.401 0 .955.042 1.468.103a8.68 8.68 0 011.141.195v3.325a8.623 8.623 0 00-.653-.036c-.886-.006-1.302.312-1.302 1.094v1.78h2.284l-.295 3.667h-1.989v7.98H9.101z"/></svg>
                </div>
                <p class="text-sm text-gray-600 font-medium">Accounts</p>
            </div>
            <p class="text-3xl font-bold text-gray-900">{{ $stats['accounts_count'] }}</p>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                </div>
                <p class="text-sm text-gray-600 font-medium">Videos</p>
            </div>
            <p class="text-3xl font-bold text-gray-900">{{ $stats['videos_count'] }}</p>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </div>
                <p class="text-sm text-gray-600 font-medium">AI Contents</p>
            </div>
            <p class="text-3xl font-bold text-gray-900">{{ $stats['ai_contents_count'] }}</p>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 bg-amber-100 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <p class="text-sm text-gray-600 font-medium">Scheduled</p>
            </div>
            <p class="text-3xl font-bold text-gray-900">{{ $stats['scheduled_posts_count'] }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        {{-- Recent Videos --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-semibold">Recent Videos</h2>
                <a href="/scraper/videos" class="text-indigo-600 hover:text-indigo-700 text-sm font-semibold">View All</a>
            </div>
            @if($recentVideos->count() > 0)
                <div class="grid grid-cols-4 gap-2">
                    @foreach($recentVideos as $video)
                        <div class="group relative bg-gray-50 rounded-xl overflow-hidden">
                            @if($video->thumbnail_url)
                                <img src="{{ $video->thumbnail_url }}" alt="" class="w-full aspect-[9/16] object-cover">
                            @else
                                <div class="w-full aspect-[9/16] bg-gradient-to-br from-indigo-100 to-purple-100 flex items-center justify-center">
                                    <svg class="w-6 h-6 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/></svg>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-center py-8">No videos yet.</p>
            @endif
        </div>

        {{-- Upcoming Posts --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-semibold">Upcoming Posts</h2>
                <a href="/scheduled-posts" class="text-indigo-600 hover:text-indigo-700 text-sm font-semibold">Manage</a>
            </div>
            @if($upcomingPosts->count() > 0)
                <div class="space-y-3">
                    @foreach($upcomingPosts as $post)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                            <div>
                                <p class="font-medium text-sm truncate max-w-[200px]">{{ $post->description ?? 'Untitled' }}</p>
                                <p class="text-xs text-gray-500">{{ $post->scheduled_time->format('M d, Y H:i') }}</p>
                            </div>
                            <span class="px-3 py-1 bg-indigo-100 text-indigo-700 text-xs font-semibold rounded-full">{{ $post->status }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-center py-8">No scheduled posts.</p>
            @endif
        </div>
    </div>

    {{-- Connected Accounts --}}
    <div class="mt-8 bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-semibold">Connected Facebook Accounts</h2>
            <a href="/auth/facebook/redirect" class="text-indigo-600 hover:text-indigo-700 text-sm font-semibold">+ Add Account</a>
        </div>
        @if($accounts->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @foreach($accounts as $account)
                    <div class="flex items-center gap-3 p-4 bg-gray-50 rounded-xl">
                        <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 24 24"><path d="M9.101 23.691v-7.98H6.627v-3.667h2.474v-1.58c0-4.085 1.848-5.978 5.858-5.978.401 0 .955.042 1.468.103a8.68 8.68 0 011.141.195v3.325a8.623 8.623 0 00-.653-.036c-.886-.006-1.302.312-1.302 1.094v1.78h2.284l-.295 3.667h-1.989v7.98H9.101z"/></svg>
                        </div>
                        <div>
                            <p class="font-medium">{{ $account->name ?? 'Facebook Account' }}</p>
                            <p class="text-xs text-gray-500">{{ $account->email }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-gray-500 text-center py-8">No accounts connected yet.</p>
        @endif
    </div>
</div>
@endsection