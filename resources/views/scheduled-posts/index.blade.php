@extends('layouts.app')

@section('title', 'Scheduled Posts')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-8">Scheduled Posts</h1>

    {{-- Create Form --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-8">
        <h2 class="text-xl font-semibold mb-4">Schedule New Post</h2>
        @if($videos->count() > 0)
            <form method="POST" action="/scheduled-posts" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @csrf
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Video</label>
                    <select name="video_id" required class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none">
                        <option value="">Select video...</option>
                        @foreach($videos as $video)
                            <option value="{{ $video->id }}">{{ Str::limit($video->title ?? 'Untitled', 50) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Platform</label>
                    <select name="platform" id="platform-select" required onchange="togglePlatformFields(this.value)" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none">
                        <option value="">Select platform...</option>
                        <option value="facebook" selected>Facebook</option>
                        <option value="youtube" {{ $user->canPostToYouTube() ? '' : 'disabled' }}>
                            YouTube {{ $user->canPostToYouTube() ? '' : '(not enabled)' }}
                        </option>
                        <option value="tiktok" {{ $user->canPostToTikTok() ? '' : 'disabled' }}>
                            TikTok {{ $user->canPostToTikTok() ? '' : '(not enabled)' }}
                        </option>
                    </select>
                </div>

                {{-- Facebook fields (shown by default) --}}
                <div id="fb-account-field">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Facebook Account</label>
                    <select name="facebook_account_id" onchange="loadPages(this.value)" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none">
                        <option value="">Select account...</option>
                        @foreach($accounts as $account)
                            <option value="{{ $account->id }}">{{ $account->name ?? $account->email }}</option>
                        @endforeach
                    </select>
                </div>
                <div id="fb-page-field">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Facebook Page</label>
                    <select name="facebook_page_id" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none">
                        <option value="">Select account first...</option>
                    </select>
                </div>

                {{-- YouTube / TikTok additional fields --}}
                <div id="platform-page-field" class="hidden">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Channel / Account ID</label>
                    <input type="text" name="platform_page_id" placeholder="Enter channel/account ID" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Description</label>
                    <input type="text" name="description" placeholder="Optional description" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Schedule Date & Time</label>
                    <input type="datetime-local" name="scheduled_time" required class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none">
                </div>
                <div class="md:col-span-2">
                    <button type="submit" class="w-full gradient-bg text-white py-3.5 rounded-xl font-bold hover:opacity-90 transition shadow-lg shadow-indigo-200">
                        Schedule Post
                    </button>
                </div>
            </form>
        @else
            <p class="text-gray-500 text-center py-8">
                No videos available. <a href="/scraper" class="text-indigo-600 font-semibold">Scrape reels</a> first.
            </p>
        @endif
    </div>

    {{-- Scheduled Posts List --}}
    <h2 class="text-xl font-semibold mb-4">All Scheduled Posts</h2>
    @if($scheduledPosts->count() > 0)
        <div class="space-y-4">
            @foreach($scheduledPosts as $post)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-2">
                                {{-- Platform badge --}}
                                <span class="px-3 py-1 text-xs font-semibold rounded-full 
                                    {{ $post->platform === 'youtube' ? 'bg-red-100 text-red-700' : ($post->platform === 'tiktok' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700') }}">
                                    {{ ucfirst($post->platform) }}
                                </span>
                                <span class="px-3 py-1 text-xs font-semibold rounded-full 
                                    {{ $post->status === 'published' ? 'bg-green-100 text-green-700' : ($post->status === 'scheduled' ? 'bg-blue-100 text-blue-700' : ($post->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700')) }}">
                                    {{ $post->status }}
                                </span>
                                <span class="text-sm text-gray-500">{{ $post->scheduled_time->format('M d, Y H:i') }}</span>
                            </div>
                            <p class="text-gray-700">{{ $post->description ?? 'No description' }}</p>
                            @if($post->video)
                                <p class="text-xs text-gray-400 mt-1">Video: {{ Str::limit($post->video->title, 80) }}</p>
                            @endif
                            @if($post->facebookAccount)
                                <p class="text-xs text-gray-400">Account: {{ $post->facebookAccount->name ?? $post->facebookAccount->email }}</p>
                            @endif
                            @if($post->published_at)
                                <p class="text-xs text-gray-400">Published at: {{ $post->published_at->format('M d, Y H:i') }}</p>
                            @endif
                            @if($post->error_message)
                                <p class="text-xs text-red-500 mt-1">Error: {{ $post->error_message }}</p>
                            @endif
                        </div>
                        <div class="flex gap-2 ml-4">
                            @if($post->status === 'pending' || $post->status === 'scheduled')
                                <form method="POST" action="/scheduled-posts/{{ $post->id }}" class="inline" onsubmit="return confirm('Delete this scheduled post?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-3 py-1.5 bg-red-600 text-white text-xs font-semibold rounded-lg hover:bg-red-700 transition">Delete</button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="mt-6">
            {{ $scheduledPosts->links() }}
        </div>
    @else
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 text-center">
            <p class="text-gray-500">No scheduled posts yet.</p>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
function loadPages(accountId) {
    const pageSelect = document.querySelector('select[name="facebook_page_id"]');
    pageSelect.innerHTML = '<option value="">Loading pages...</option>';
    
    if (!accountId) {
        pageSelect.innerHTML = '<option value="">Select account first...</option>';
        return;
    }
    
    fetch(`/facebook/pages/${accountId}`)
        .then(res => res.json())
        .then(pages => {
            if (pages.length === 0) {
                pageSelect.innerHTML = '<option value="">No pages found</option>';
                return;
            }
            pageSelect.innerHTML = '<option value="">Select page...</option>' + 
                pages.map(p => `<option value="${p.id}">${p.name}</option>`).join('');
        })
        .catch(() => {
            pageSelect.innerHTML = '<option value="">Error loading pages</option>';
        });
}

function togglePlatformFields(platform) {
    const fbAccountField = document.getElementById('fb-account-field');
    const fbPageField = document.getElementById('fb-page-field');
    const platformPageField = document.getElementById('platform-page-field');
    const fbAccountSelect = document.querySelector('select[name="facebook_account_id"]');
    const fbPageSelect = document.querySelector('select[name="facebook_page_id"]');
    const platformPageInput = document.querySelector('input[name="platform_page_id"]');

    if (platform === 'facebook') {
        fbAccountField.classList.remove('hidden');
        fbPageField.classList.remove('hidden');
        platformPageField.classList.add('hidden');
        fbAccountSelect.required = true;
        fbPageSelect.required = true;
        platformPageInput.required = false;
    } else {
        fbAccountField.classList.add('hidden');
        fbPageField.classList.add('hidden');
        platformPageField.classList.remove('hidden');
        fbAccountSelect.required = false;
        fbPageSelect.required = false;
        platformPageInput.required = false;

        if (platform === 'youtube') {
            platformPageInput.placeholder = 'Enter YouTube channel ID';
        } else if (platform === 'tiktok') {
            platformPageInput.placeholder = 'Enter TikTok account ID';
        }
    }
}
</script>
@endpush