@extends('layouts.app')

@section('title', 'Scraper Dashboard')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-8">Scraper Dashboard</h1>

    {{-- Tab Navigation --}}
    <div class="mb-8 border-b border-gray-200" x-data="{ activeTab: 'scrape' }">
        <nav class="flex gap-6 -mb-px">
            <button @click="activeTab = 'scrape'" :class="activeTab === 'scrape' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="py-4 px-1 border-b-2 font-semibold text-sm transition whitespace-nowrap">
                📥 Scrape Reels
            </button>
            <button @click="activeTab = 'keyword'" :class="activeTab === 'keyword' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="py-4 px-1 border-b-2 font-semibold text-sm transition whitespace-nowrap">
                🔍 Search by Keyword
            </button>
            <button @click="activeTab = 'user'" :class="activeTab === 'user' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="py-4 px-1 border-b-2 font-semibold text-sm transition whitespace-nowrap">
                👤 Search by User
            </button>
            <button @click="activeTab = 'videos'" :class="activeTab === 'videos' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="py-4 px-1 border-b-2 font-semibold text-sm transition whitespace-nowrap">
                🎬 Recent Videos
            </button>
        </nav>

        {{-- Tab Content: Scrape --}}
        <div x-show="activeTab === 'scrape'" x-cloak class="py-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                {{-- Connected Accounts --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-xl font-semibold mb-4">Connected Facebook Accounts</h2>
                    @if($accounts->count() > 0)
                        <div class="space-y-4">
                            @foreach($accounts as $account)
                                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
                                    <div>
                                        <p class="font-medium">{{ $account->name ?? $account->facebook_user_id }}</p>
                                        <p class="text-sm text-gray-500">{{ $account->email }}</p>
                                    </div>
                                    <button onclick="fetchPages({{ $account->id }})" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition text-sm">
                                        Fetch Pages
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500">No Facebook accounts connected.</p>
                        <a href="/auth/facebook/redirect" class="inline-block mt-4 px-6 py-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition">
                            Connect Facebook Account
                        </a>
                    @endif
                </div>

                {{-- Scrape Form --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-xl font-semibold mb-4">Scrape Reels</h2>
                    <p class="text-sm text-gray-500 mb-4">Scrape reels from a specific Facebook Page ID.</p>
                    <form id="scrapeForm" method="POST" action="/scraper/scrape" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Facebook Account</label>
                            <select name="facebook_account_id" required class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none">
                                <option value="">Select account</option>
                                @foreach($accounts as $account)
                                    <option value="{{ $account->id }}">{{ $account->name ?? $account->facebook_user_id }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Page ID</label>
                            <input type="text" name="page_id" required placeholder="Facebook Page ID" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Limit</label>
                            <input type="number" name="limit" value="50" min="1" max="100" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none">
                        </div>
                        <button type="submit" class="w-full gradient-bg text-white py-3.5 rounded-xl font-bold hover:opacity-90 transition shadow-lg shadow-indigo-200">
                            Start Scraping
                        </button>
                    </form>
                    @if(session('success'))
                        <div class="mt-4 bg-green-50 text-green-700 p-4 rounded-xl">
                            <p class="font-semibold">{{ session('success') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Tab Content: Search by Keyword --}}
        <div x-show="activeTab === 'keyword'" x-cloak class="py-6">
            <div class="max-w-2xl mx-auto bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-xl font-semibold mb-4">🔍 Search by Keyword</h2>
                <p class="text-sm text-gray-500 mb-4">Search reels containing specific keywords across all your pages (dispatched to queue).</p>
                <form method="POST" action="/scraper/search/keyword" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Facebook Account</label>
                        <select name="facebook_account_id" required class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none">
                            <option value="">Select account</option>
                            @foreach($accounts as $account)
                                <option value="{{ $account->id }}">{{ $account->name ?? $account->facebook_user_id }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Keyword</label>
                        <input type="text" name="keyword" required placeholder="e.g. tutorial, funny, cooking..." class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Limit per Page</label>
                        <input type="number" name="limit" value="20" min="1" max="100" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none">
                    </div>
                    <button type="submit" class="w-full bg-yellow-500 text-white py-3.5 rounded-xl font-bold hover:bg-yellow-600 transition shadow-lg shadow-yellow-200">
                        🔍 Search by Keyword
                    </button>
                </form>
            </div>
        </div>

        {{-- Tab Content: Search by User --}}
        <div x-show="activeTab === 'user'" x-cloak class="py-6">
            <div class="max-w-2xl mx-auto bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-xl font-semibold mb-4">👤 Search by User / Author</h2>
                <p class="text-sm text-gray-500 mb-4">Find reels posted by a specific user name or ID across your pages.</p>
                <form method="POST" action="/scraper/search/user" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Facebook Account</label>
                        <select name="facebook_account_id" required class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none">
                            <option value="">Select account</option>
                            @foreach($accounts as $account)
                                <option value="{{ $account->id }}">{{ $account->name ?? $account->facebook_user_id }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">User Name / ID</label>
                        <input type="text" name="user_id" required placeholder="e.g. John Doe, username, page ID..." class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Limit</label>
                        <input type="number" name="limit" value="20" min="1" max="100" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none">
                    </div>
                    <button type="submit" class="w-full bg-blue-500 text-white py-3.5 rounded-xl font-bold hover:bg-blue-600 transition shadow-lg shadow-blue-200">
                        👤 Search by User
                    </button>
                </form>
            </div>
        </div>

        {{-- Tab Content: Recent Videos --}}
        <div x-show="activeTab === 'videos'" x-cloak class="py-6">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-semibold">Recent Videos</h2>
                    <a href="/scraper/videos" class="text-indigo-600 hover:text-indigo-700 font-semibold text-sm">View All →</a>
                </div>
                @if($recentVideos->count() > 0)
                    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-4">
                        @foreach($recentVideos as $video)
                            <div class="group relative bg-gray-50 rounded-xl overflow-hidden hover:shadow-md transition">
                                @if($video->thumbnail_url)
                                    <img src="{{ $video->thumbnail_url }}" alt="{{ $video->title }}" class="w-full aspect-[9/16] object-cover">
                                @else
                                    <div class="w-full aspect-[9/16] bg-gradient-to-br from-indigo-100 to-purple-100 flex items-center justify-center">
                                        <svg class="w-12 h-12 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </div>
                                @endif
                                <div class="p-2">
                                    <p class="text-sm font-medium truncate">{{ $video->title }}</p>
                                    @if($video->author_name)
                                        <p class="text-xs text-gray-400 truncate">by {{ $video->author_name }}</p>
                                    @endif
                                    <div class="flex items-center gap-1 mt-1">
                                        <span class="text-xs px-1.5 py-0.5 rounded-full 
                                            @if($video->source_type === 'keyword') bg-yellow-100 text-yellow-700
                                            @elseif($video->source_type === 'user') bg-blue-100 text-blue-700
                                            @else bg-gray-100 text-gray-600
                                            @endif">
                                            {{ $video->source_type }}
                                        </span>
                                        <p class="text-xs text-gray-400">{{ $video->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-center py-8">No videos scraped yet. Start scraping to see results.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
async function fetchPages(accountId) {
    try {
        const response = await fetch(`/scraper/pages/${accountId}`, {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('input[name="_token"]')?.value || '' }
        });
        const data = await response.json();
        if (data.success && data.pages.length > 0) {
            const pageInput = document.querySelector('input[name="page_id"]');
            const pagesList = data.pages.map(p => `${p.id} - ${p.name}`).join('\n');
            alert(`Available pages:\n${pagesList}`);
        } else {
            alert('No pages found or failed to fetch.');
        }
    } catch (error) {
        alert('Error: ' + error.message);
    }
}
</script>
@endpush