@extends('layouts.app')

@section('title', 'Video Library')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-8">
        <h1 class="text-3xl font-bold">Video Library</h1>
        <a href="/scraper" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition text-sm font-semibold">
            ← Back to Scraper
        </a>
    </div>

    {{-- Search & Filter --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-8">
        <form method="GET" action="{{ route('scraper.videos', [], false) }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Title, description, keyword, author..." class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Source Type</label>
                <select name="source_type" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none">
                    <option value="">All</option>
                    <option value="page" {{ request('source_type') === 'page' ? 'selected' : '' }}>Page Scrape</option>
                    <option value="keyword" {{ request('source_type') === 'keyword' ? 'selected' : '' }}>Keyword Search</option>
                    <option value="user" {{ request('source_type') === 'user' ? 'selected' : '' }}>User Search</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Facebook Account</label>
                <select name="facebook_account_id" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none">
                    <option value="">All</option>
                    @foreach($accounts as $acc)
                        <option value="{{ $acc->id }}" {{ request('facebook_account_id') == $acc->id ? 'selected' : '' }}>{{ $acc->name ?? $acc->facebook_user_id }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full gradient-bg text-white py-3 rounded-xl font-bold hover:opacity-90 transition shadow-lg shadow-indigo-200">
                    Filter
                </button>
            </div>
        </form>
    </div>

    {{-- Videos Grid --}}
    @if($videos->count() > 0)
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-4">
            @foreach($videos as $video)
                <div x-data="{ showModal: false, playVideo: false }" class="group relative bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg transition">
                    <div class="cursor-pointer relative" @click="showModal = true">
                        @if($video->thumbnail_url)
                            <img src="{{ $video->thumbnail_url }}" alt="{{ $video->title }}" class="w-full aspect-[9/16] object-cover">
                        @else
                            <div class="w-full aspect-[9/16] bg-gradient-to-br from-indigo-100 to-purple-100 flex items-center justify-center">
                                <svg class="w-12 h-12 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                        @endif
                        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 transition flex items-center justify-center">
                            <svg class="w-12 h-12 text-white opacity-0 group-hover:opacity-100 drop-shadow-md" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                    </div>
                    <div class="p-3">
                        <p class="text-sm font-medium truncate">{{ $video->title }}</p>
                        @if($video->author_name)
                            <p class="text-xs text-gray-400 truncate">by {{ $video->author_name }}</p>
                        @endif
                        <div class="flex items-center gap-2 mt-1">
                            <span class="text-xs px-2 py-0.5 rounded-full 
                                @if($video->source_type === 'keyword') bg-yellow-100 text-yellow-700
                                @elseif($video->source_type === 'user') bg-blue-100 text-blue-700
                                @else bg-gray-100 text-gray-600
                                @endif">
                                {{ $video->source_type }}
                            </span>
                            <span class="text-xs text-gray-400">{{ $video->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                    <form action="/scraper/videos/{{ $video->id }}" method="POST" class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition" onsubmit="return confirm('Delete this video?')">
                        @csrf
                        @method('DELETE')
                        <button class="p-2 bg-red-500 text-white rounded-lg hover:bg-red-600 text-xs">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </form>

                    {{-- Modal for Video/Thumbnail Preview --}}
                    <div x-show="showModal" 
                         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black bg-opacity-75"
                         style="display: none;">
                        <div class="relative bg-white rounded-2xl w-full max-w-xl max-h-[90vh] overflow-hidden flex flex-col" @click.away="showModal = false; playVideo = false">
                            <div class="p-4 flex justify-between items-center border-b border-gray-100">
                                <h3 class="font-bold text-lg truncate pr-4">{{ $video->title }}</h3>
                                <button type="button" @click="showModal = false; playVideo = false" class="text-gray-500 hover:text-gray-800 focus:outline-none">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                            <div class="relative flex-1 bg-black flex items-center justify-center overflow-hidden min-h-[300px]">
                                <template x-if="!playVideo">
                                    <div class="w-full h-full flex flex-col items-center justify-center py-8">
                                        @if($video->thumbnail_url)
                                            <img src="{{ $video->thumbnail_url }}" alt="{{ $video->title }}" class="max-h-[50vh] object-contain mb-6">
                                        @else
                                            <div class="h-48 w-full flex items-center justify-center text-gray-400">No thumbnail available</div>
                                        @endif
                                        <button type="button" @click="playVideo = true" class="px-6 py-3 bg-indigo-600 text-white font-bold rounded-xl shadow hover:bg-indigo-700 flex items-center gap-2 focus:outline-none">
                                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                            Start Video
                                        </button>
                                    </div>
                                </template>
                                <template x-if="playVideo">
                                    <video controls autoplay class="w-full max-h-[70vh] object-contain bg-black">
                                        <source src="{{ $video->source_url }}" type="video/mp4">
                                        Your browser does not support the video tag.
                                    </video>
                                </template>
                            </div>
                            <div class="p-4 bg-gray-50 border-t border-gray-100 flex justify-end gap-3">
                                <button type="button" @click="showModal = false; playVideo = false" class="px-4 py-2 text-gray-600 font-medium hover:text-gray-800 focus:outline-none">Close</button>
                            </div>
                        </div>
                    </div>

                </div>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $videos->links() }}
        </div>
    @else
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
            <p class="text-gray-500 text-lg mb-2">No videos found</p>
            <p class="text-gray-400 text-sm">Try different search terms or scrape new reels.</p>
            <a href="/scraper" class="inline-block mt-4 px-6 py-3 gradient-bg text-white rounded-xl font-bold hover:opacity-90 transition shadow-lg shadow-indigo-200">
                Go to Scraper
            </a>
        </div>
    @endif
</div>
@endsection