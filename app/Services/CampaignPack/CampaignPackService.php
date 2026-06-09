<?php

namespace App\Services\CampaignPack;

use App\Models\Brand;
use App\Models\Campaign;
use App\Models\Post;
use App\Services\Ai\AiProviderException;
use App\Services\Ai\AiProviderFactory;
use App\Services\Ai\ChecksGenerationLimit;
use Carbon\Carbon;
use Illuminate\Support\Str;

class CampaignPackService
{
    use ChecksGenerationLimit;

    public function __construct(private readonly AiProviderFactory $factory) {}

    /**
     * Generate a full post sequence for a campaign using the AI provider.
     * Creates Post drafts linked to the campaign and marks campaign active.
     *
     * @param  array<string, mixed>  $pack
     *
     * @throws AiProviderException|\RuntimeException
     */
    public function generate(Campaign $campaign, Brand $brand, array $pack): Campaign
    {
        $this->enforceLimit($brand);

        $provider = $this->factory->make();
        $system = $this->buildSystemPrompt($brand);
        $user = $this->buildUserPrompt($campaign, $brand, $pack);
        $raw = $provider->generate($system, $user, 8192);
        $posts = $this->parseResponse($raw);

        $this->savePosts($campaign, $brand, $posts);
        $campaign->update(['status' => 'active']);

        $this->incrementUsage($brand);

        return $campaign->fresh();
    }

    // ── Prompts ───────────────────────────────────────────────────────────────

    private function buildSystemPrompt(Brand $brand): string
    {
        $voice = $brand->brand_voice
            ? 'Brand Voice Profile: '.json_encode($brand->brand_voice)
            : '';

        $profile = implode("\n", array_filter([
            $brand->name ? "Brand: {$brand->name}" : null,
            $brand->tagline ? "Tagline: {$brand->tagline}" : null,
            $brand->description ? "What they do: {$brand->description}" : null,
            $brand->target_audience ? "Audience: {$brand->target_audience}" : null,
            $brand->positioning ? "Positioning: {$brand->positioning}" : null,
            $brand->negative_brief ? "Never say: {$brand->negative_brief}" : null,
            $voice ?: null,
        ]));

        return <<<PROMPT
You are Brandara, an expert social media campaign strategist for African B2B businesses.

{$profile}

RULES:
- No two posts should feel like duplicates — vary the angle, format, and hook.
- Build campaign arc: awareness → desire → action.
- Every post must end with a question or call to action.
- Adapt each post natively to its platform (tone, length, hashtag count).
- Never use corporate jargon like synergy, leverage, game-changer.
- WhatsApp copy is warm and direct — no hashtags.
- Return valid JSON only. No markdown fences. No explanation.
PROMPT;
    }

    private function buildUserPrompt(Campaign $campaign, Brand $brand, array $pack): string
    {
        $platforms = implode(', ', $campaign->platforms ?? ['linkedin']);
        $startDate = $campaign->start_date?->format('Y-m-d') ?? now()->format('Y-m-d');
        $durationDays = $campaign->start_date && $campaign->end_date
            ? max(1, (int) $campaign->start_date->diffInDays($campaign->end_date) + 1)
            : ($pack['duration_days'] ?? 5);

        $toneMap = [
            'professional' => 'polished and professional',
            'founder' => 'raw founder voice — honest and personal',
            'african' => 'warm, culturally resonant African business voice',
            'friendly' => 'conversational and approachable',
            'bold' => 'bold, provocative, attention-grabbing',
            'authority' => 'confident expert positioning',
        ];
        $tone = $toneMap[$pack['default_tone'] ?? 'professional'] ?? 'professional';

        return <<<PROMPT
CAMPAIGN: {$campaign->name}
PACK TYPE: {$pack['name']}
GOAL: {$campaign->goal}
KEY MESSAGE: {$campaign->key_message}
DURATION: {$durationDays} days starting {$startDate}
PLATFORMS: {$platforms}
TONE: {$tone}

Generate a {$durationDays}-day campaign with one post per day.

Return this exact JSON:
{
  "campaign_summary": "1-sentence description of the campaign arc",
  "whatsapp_broadcast": "Opening broadcast message to send on day 1 (warm, direct, no hashtags)",
  "posts": [
    {
      "day": 1,
      "post_type": "awareness",
      "angle": "Short hook description",
      "platforms": {
        "linkedin": {"body": "...", "hashtags": ["#tag1"]},
        "twitter": {"body": "..."},
        "facebook": {"body": "..."},
        "instagram": {"body": "...", "hashtags": ["#tag1", "#tag2"]},
        "whatsapp": {"body": "..."},
        "threads": {"body": "..."},
        "tiktok": {"body": "..."}
      }
    }
  ]
}

Only include platform keys that are in the PLATFORMS list above.
Respond with valid JSON only.
PROMPT;
    }

    // ── Parser ────────────────────────────────────────────────────────────────

    /**
     * @return array<int, array<string, mixed>>
     */
    private function parseResponse(string $raw): array
    {
        $clean = preg_replace('/^```json\s*/m', '', $raw);
        $clean = preg_replace('/^```\s*/m', '', $clean);
        $clean = trim($clean);

        $data = json_decode($clean, true);

        if (json_last_error() !== JSON_ERROR_NONE || ! is_array($data)) {
            return [];
        }

        return $data['posts'] ?? [];
    }

    // ── Persistence ───────────────────────────────────────────────────────────

    /**
     * @param  array<int, array<string, mixed>>  $posts
     */
    private function savePosts(Campaign $campaign, Brand $brand, array $posts): void
    {
        $startDate = $campaign->start_date ?? now();

        foreach ($posts as $postData) {
            $day = (int) ($postData['day'] ?? 1);
            $scheduledAt = Carbon::parse($startDate)->addDays($day - 1)->setTime(9, 0, 0);

            Post::create([
                'id' => Str::uuid()->toString(),
                'brand_id' => $brand->id,
                'campaign_id' => $campaign->id,
                'created_by' => auth()->id(),
                'input_type' => 'product',
                'raw_input' => $campaign->key_message,
                'ai_generated' => true,
                'variation_selected' => null,
                'platform_contents' => $postData['platforms'] ?? [],
                'tone' => $campaign->tone ?? 'professional',
                'status' => 'draft',
                'scheduled_at' => $scheduledAt,
            ]);
        }
    }
}
