<?php

namespace App\Services;

use App\Models\Video;
use App\Models\AiContent;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiContentService
{
    private string $geminiApiKey;

    public function __construct()
    {
        $this->geminiApiKey = config('services.gemini.api_key', env('AI_GEMINI_API_KEY', ''));
        $this->naimApiKey = env('AI_NAIM_ROUTER_KEY', '');
    }

    /**
     * Generate AI content from a video.
     */
    public function generateContent(Video $video, string $style = 'rewrite'): ?AiContent
    {
        $provider = \App\Models\Setting::getVal('ai_provider', 'gemini');

        if ($provider === 'gemini' && !$this->geminiApiKey) {
            Log::warning('Gemini API key not configured');
            return null;
        }

        if ($provider === 'naim' && !$this->naimApiKey) {
            Log::warning('Naim Router API key not configured');
            return null;
        }

        $description = $video->description ?? $video->title ?? '';

        if (empty($description)) {
            return null;
        }

        $prompt = $this->buildPrompt($description, $style);

        try {
            $response = Http::post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key={$this->geminiApiKey}", [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.7,
                    'maxOutputTokens' => 1024,
                ]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $generatedText = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';

                if (!empty($generatedText)) {
                    return AiContent::create([
                        'user_id' => $video->user_id,
                        'video_id' => $video->id,
                        'original_content' => $description,
                        'generated_content' => $generatedText,
                        'style' => $style,
                        'status' => 'generated',
                    ]);
                }
            }

            Log::error('Gemini API error', [
                'status' => $response->status(),
                'body' => $response->json(),
            ]);
        } catch (\Exception $e) {
            Log::error('AI generation exception', [
                'message' => $e->getMessage(),
            ]);
        }

        return null;
    }

    /**
     * Build prompt based on style.
     */
    private function buildPrompt(string $description, string $style): string
    {
        return match ($style) {
            'rewrite' => "Rewrite the following Facebook Reel description to make it more engaging and viral. Keep it under 200 characters:\n\n{$description}",
            'caption' => "Generate an engaging caption for a Facebook Reel based on this description. Make it catchy and use relevant hashtags. Keep under 300 characters:\n\n{$description}",
            'hashtags' => "Generate 15-20 relevant hashtags for a Facebook Reel about:\n\n{$description}",
            'seo' => "Create an SEO-optimized title and description for a Facebook Reel video about:\n\n{$description}\n\nFormat:\nTitle: [SEO title]\nDescription: [SEO description with keywords]",
            'summary' => "Summarize this Facebook Reel description in 2-3 sentences:\n\n{$description}",
            default => "Create engaging content for this Facebook Reel:\n\n{$description}",
        };
    }

    /**
     * Generate content for multiple videos.
     */
    public function generateBatch(array $videoIds, string $style = 'rewrite'): array
    {
        $results = [];

        foreach ($videoIds as $videoId) {
            $video = Video::find($videoId);
            if ($video) {
                $result = $this->generateContent($video, $style);
                $results[$videoId] = $result ? 'success' : 'failed';
            }
        }

        return $results;
    }
}