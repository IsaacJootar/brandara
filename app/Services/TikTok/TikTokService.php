<?php

namespace App\Services\TikTok;

use App\Models\Brand;
use App\Services\Ai\AiProviderException;
use App\Services\Ai\AiProviderFactory;

class TikTokService
{
    public function __construct(private readonly AiProviderFactory $factory) {}

    /**
     * Generate a complete TikTok content toolkit for the given topic.
     *
     * @return array{
     *   caption: string,
     *   hashtags: string[],
     *   script: array{hook_seconds_1_to_3: string, content_body: string, cta_closing: string, total_duration: string},
     *   text_overlays: array<int, array{timing: string, text: string}>,
     *   bio_copy: string,
     *   content_tips: string
     * }
     *
     * @throws AiProviderException
     */
    public function generate(Brand $brand, string $topic, string $tone): array
    {
        $provider = $this->factory->make();

        $system = $this->buildSystemPrompt($brand);
        $user = $this->buildUserPrompt($brand, $topic, $tone);

        $raw = $provider->generate($system, $user, 4096);

        return $this->parse($raw);
    }

    // ── Prompts ───────────────────────────────────────────────────────────────

    private function buildSystemPrompt(Brand $brand): string
    {
        $voice = $brand->brand_voice
            ? 'Brand Voice: '.($brand->brand_voice['writing_summary'] ?? json_encode($brand->brand_voice))
            : '';

        $profile = implode("\n", array_filter([
            $brand->name ? "Brand: {$brand->name}" : null,
            $brand->description ? "What they do: {$brand->description}" : null,
            $brand->target_audience ? "Audience: {$brand->target_audience}" : null,
            $brand->negative_brief ? "Never say: {$brand->negative_brief}" : null,
            $voice ?: null,
        ]));

        return <<<PROMPT
You are writing TikTok content for an African business or professional.
TikTok requires a completely different approach from LinkedIn or Instagram.

{$profile}

TIKTOK RULES:
1. The hook happens in the first 3 seconds — or viewers swipe away.
2. Start with a question, surprising statement, or a bold claim.
3. Content should feel authentic and slightly unpolished — not corporate.
4. Script is written as if spoken aloud — natural speech, not prose.
5. Text overlays must be short punchy phrases (3–6 words max).
6. Hashtags: mix trending (#BusinessTips, #Entrepreneur) with local niche tags.
7. Bio copy is optimised for TikTok search — keywords + clear value + CTA.
8. Never use corporate jargon.
9. Return valid JSON only. No markdown fences. No extra explanation.
PROMPT;
    }

    private function buildUserPrompt(Brand $brand, string $topic, string $tone): string
    {
        $toneMap = [
            'professional' => 'polished but approachable',
            'founder' => 'raw founder voice — honest and direct',
            'african' => 'warm, culturally resonant African voice',
            'friendly' => 'conversational and energetic',
            'bold' => 'bold and provocative',
            'educational' => 'clear, educational, value-driven',
        ];

        $toneDescription = $toneMap[$tone] ?? 'conversational and energetic';

        return <<<PROMPT
BRAND: {$brand->name}
AUDIENCE: {$brand->target_audience}
CONTENT TOPIC: {$topic}
TONE: {$toneDescription}

Generate a complete TikTok content toolkit. Return this exact JSON:
{
  "caption": "TikTok caption under 150 characters (catchy hook + 2-3 hashtags inline)",
  "hashtags": ["#tag1", "#tag2", "#tag3", "#tag4", "#tag5", "#tag6", "#tag7", "#tag8"],
  "script": {
    "hook_seconds_1_to_3": "Exact opening words for the first 3 seconds — punchy, attention-grabbing",
    "content_body": "Rest of the script (45–60 seconds of natural speech). Include line breaks between points.",
    "cta_closing": "Final call to action (5–10 seconds)",
    "total_duration": "Estimated total in seconds (e.g. 58 seconds)"
  },
  "text_overlays": [
    {"timing": "0–3s", "text": "Hook overlay (3–5 words)"},
    {"timing": "8–12s", "text": "Key point overlay"},
    {"timing": "20–25s", "text": "Value point overlay"},
    {"timing": "end", "text": "CTA overlay"}
  ],
  "bio_copy": "Optimised TikTok bio under 80 characters — keywords + value + CTA",
  "content_tips": "2-3 practical filming tips for this specific video"
}

Respond with valid JSON only.
PROMPT;
    }

    // ── Parser ────────────────────────────────────────────────────────────────

    /**
     * @return array<string, mixed>
     */
    private function parse(string $raw): array
    {
        $clean = preg_replace('/^```json\s*/m', '', $raw);
        $clean = preg_replace('/^```\s*/m', '', $clean);
        $clean = trim($clean);

        $data = json_decode($clean, true);

        if (json_last_error() !== JSON_ERROR_NONE || ! is_array($data)) {
            return [
                'caption' => $raw,
                'hashtags' => [],
                'script' => [
                    'hook_seconds_1_to_3' => '',
                    'content_body' => $raw,
                    'cta_closing' => '',
                    'total_duration' => '',
                ],
                'text_overlays' => [],
                'bio_copy' => '',
                'content_tips' => '',
            ];
        }

        return $data;
    }
}
