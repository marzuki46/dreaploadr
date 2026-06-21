<?php

namespace App\Http\Controllers;

use App\Models\AiContent;
use App\Models\Video;
use App\Services\AiContentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AiContentController extends Controller
{
    public function __construct(
        protected AiContentService $aiContentService
    ) {}

    /**
     * Show AI content dashboard.
     */
    public function index()
    {
        $videos = Video::where('user_id', Auth::id())->latest()->get();
        $aiContents = AiContent::where('user_id', Auth::id())
            ->with('video')
            ->latest()
            ->paginate(15);

        return view('ai-content.index', compact('videos', 'aiContents'));
    }

    /**
     * Generate AI content.
     */
    public function generate(Request $request)
    {
        $data = $request->validate([
            'video_id' => ['required', 'exists:videos,id'],
            'style' => ['required', 'string', 'in:rewrite,caption,hashtags,seo,summary'],
        ]);

        $video = Video::findOrFail($data['video_id']);

        if ($video->user_id !== Auth::id()) {
            abort(403);
        }

        $aiContent = $this->aiContentService->generateContent($video, $data['style']);

        if ($aiContent) {
            return response()->json([
                'success' => true,
                'message' => 'AI content generated successfully.',
                'content' => $aiContent,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Failed to generate AI content. Make sure GEMINI_API_KEY is set and the video has a description.',
        ], 422);
    }

    /**
     * Save/edit AI content.
     */
    public function save(Request $request, AiContent $aiContent)
    {
        if ($aiContent->user_id !== Auth::id()) {
            abort(403);
        }

        $data = $request->validate([
            'generated_content' => ['required', 'string'],
            'status' => ['nullable', 'string', 'in:draft,approved,rejected'],
        ]);

        $aiContent->update([
            'ai_remake_text' => $data['generated_content'],
            'status' => $data['status'] ?? 'draft',
        ]);

        return back()->with('success', 'Content saved successfully.');
    }
}