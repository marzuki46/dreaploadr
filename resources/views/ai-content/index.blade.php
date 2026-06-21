@extends('layouts.app')

@section('title', 'AI Content Remaker')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-8">AI Content Remaker</h1>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        {{-- Generate Form --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-xl font-semibold mb-4">Generate AI Content</h2>
            @if($videos->count() > 0)
                <form id="aiGenerateForm" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Select Video</label>
                        <select name="video_id" required class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none">
                            <option value="">Choose a video...</option>
                            @foreach($videos as $video)
                                <option value="{{ $video->id }}">{{ Str::limit($video->title ?? 'Video #'.$video->id, 60) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Style</label>
                        <select name="style" required class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none">
                            <option value="rewrite">Rewrite Description</option>
                            <option value="caption">Generate Caption</option>
                            <option value="hashtags">Generate Hashtags</option>
                            <option value="seo">SEO Optimize</option>
                            <option value="summary">Summary</option>
                        </select>
                    </div>
                    <button type="submit" class="w-full gradient-bg text-white py-3.5 rounded-xl font-bold hover:opacity-90 transition shadow-lg shadow-indigo-200">
                        Generate with AI ✨
                    </button>
                </form>
                <div id="aiResult" class="mt-4 hidden"></div>
            @else
                <p class="text-gray-500 text-center py-8">No videos available. <a href="/scraper" class="text-indigo-600 font-semibold">Scrape some reels</a> first.</p>
            @endif
        </div>

        {{-- Quick Stats --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-xl font-semibold mb-4">Content Stats</h2>
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-green-50 p-4 rounded-xl">
                    <p class="text-2xl font-bold text-green-700">{{ $aiContents->total() }}</p>
                    <p class="text-sm text-green-600 font-medium">Total Generated</p>
                </div>
                <div class="bg-blue-50 p-4 rounded-xl">
                    <p class="text-2xl font-bold text-blue-700">{{ $aiContents->where('status', 'approved')->count() }}</p>
                    <p class="text-sm text-blue-600 font-medium">Approved</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Generated Contents --}}
    <div class="mt-8">
        <h2 class="text-xl font-semibold mb-4">Generated Contents</h2>
        @if($aiContents->count() > 0)
            <div class="space-y-4">
                @foreach($aiContents as $content)
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <div class="flex items-start justify-between mb-3">
                            <div>
                                <span class="px-3 py-1 bg-indigo-100 text-indigo-700 text-xs font-semibold rounded-full">{{ $content->style }}</span>
                                <span class="px-3 py-1 ml-2 {{ $content->status === 'approved' ? 'bg-green-100 text-green-700' : ($content->status === 'generated' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-700') }} text-xs font-semibold rounded-full">{{ $content->status }}</span>
                            </div>
                            <small class="text-gray-400">{{ $content->created_at->diffForHumans() }}</small>
                        </div>
                        @if($content->video)
                            <p class="text-xs text-gray-500 mb-2">From: <a href="{{ $content->video->video_url ?? '#' }}" target="_blank" class="text-indigo-600 hover:underline">{{ Str::limit($content->video->title, 60) }}</a></p>
                        @endif
                        <div class="bg-gray-50 rounded-xl p-4 mb-3">
                            <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $content->generated_content }}</p>
                        </div>
                        <div class="flex gap-2">
                            <form method="POST" action="/ai-content/{{ $content->id }}/save">
                                @csrf
                                <input type="hidden" name="generated_content" value="{{ $content->generated_content }}">
                                <input type="hidden" name="status" value="approved">
                                <button type="submit" class="px-4 py-2 bg-green-600 text-white text-sm font-semibold rounded-lg hover:bg-green-700 transition">Approve</button>
                            </form>
                            <form method="POST" action="/ai-content/{{ $content->id }}/save">
                                @csrf
                                <input type="hidden" name="generated_content" value="{{ $content->generated_content }}">
                                <input type="hidden" name="status" value="draft">
                                <button type="submit" class="px-4 py-2 bg-gray-600 text-white text-sm font-semibold rounded-lg hover:bg-gray-700 transition">Save as Draft</button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="mt-6">
                {{ $aiContents->links() }}
            </div>
        @else
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 text-center">
                <p class="text-gray-500">No AI content generated yet. Select a video and generate content above.</p>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
document.getElementById('aiGenerateForm')?.addEventListener('submit', async function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const resultDiv = document.getElementById('aiResult');
    resultDiv.className = 'mt-4';
    resultDiv.innerHTML = '<div class="bg-indigo-50 text-indigo-700 p-4 rounded-xl"><p class="font-semibold">✨ Generating AI content...</p></div>';
    resultDiv.classList.remove('hidden');

    try {
        const response = await fetch('/ai-content/generate', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'Accept': 'application/json',
            },
            body: formData,
        });
        const data = await response.json();
        if (data.success) {
            resultDiv.innerHTML = `
                <div class="bg-green-50 text-green-700 p-4 rounded-xl">
                    <p class="font-semibold">✅ Content generated successfully!</p>
                    <p class="text-sm mt-2 whitespace-pre-wrap">${data.content.generated_content}</p>
                </div>
            `;
            setTimeout(() => location.reload(), 1500);
        } else {
            resultDiv.innerHTML = `<div class="bg-red-50 text-red-700 p-4 rounded-xl"><p>${data.message}</p></div>`;
        }
    } catch (error) {
        resultDiv.innerHTML = `<div class="bg-red-50 text-red-700 p-4 rounded-xl"><p>Error: ${error.message}</p></div>`;
    }
});
</script>
@endpush
@endsection