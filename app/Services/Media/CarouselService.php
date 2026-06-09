<?php

namespace App\Services\Media;

use App\Models\Brand;
use App\Services\Ai\AiProviderException;
use App\Services\Ai\AiProviderFactory;
use App\Services\Ai\ChecksGenerationLimit;

class CarouselService
{
    use ChecksGenerationLimit;

    public function __construct(private readonly AiProviderFactory $factory) {}

    /** @throws AiProviderException|\RuntimeException */
    public function generate(Brand $brand, string $topic, string $platform, string $structure): array
    {
        $this->enforceLimit($brand);

        $provider = $this->factory->make();
        $raw = $provider->generate($this->buildSystemPrompt($brand), $this->buildUserPrompt($brand, $topic, $platform, $structure), 4096);
        $result = $this->parse($raw);

        $this->incrementUsage($brand);

        return $result;
    }

    /** @throws AiProviderException|\RuntimeException */
    public function generateQuoteCards(Brand $brand, string $input, string $cardType): array
    {
        $this->enforceLimit($brand);

        $provider = $this->factory->make();
        $raw = $provider->generate($this->buildQuoteSystemPrompt($brand), $this->buildQuoteUserPrompt($input, $cardType), 2048);
        $result = $this->parseQuote($raw);

        $this->incrementUsage($brand);

        return $result;
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
You are a carousel copywriter for African B2B founders and consultants.
Carousels are swiped — every slide must earn the swipe.

{$profile}

CAROUSEL RULES:
1. Hook slide (slide 1): single bold statement or question that stops the scroll.
2. Content slides: one idea per slide — never more than 3 short lines of body copy.
3. CTA slide (final): one clear next step. Never "like and follow" — be specific.
4. Visual direction note per slide: brief lighting/mood/composition suggestion for Canva.
5. Copy should feel like a conversation, not a lecture.
6. Never use AI buzzwords: "delve", "leverage", "unlock", "game-changer".
7. Return valid JSON only. No markdown. No explanation.
PROMPT;
    }

    private function buildUserPrompt(Brand $brand, string $topic, string $platform, string $structure): string
    {
        $slideRange = match ($platform) {
            'linkedin' => '8–12 slides',
            'instagram' => '5–8 slides',
            default => '6–10 slides',
        };

        $structureDesc = match ($structure) {
            'problem-solution' => 'Problem → Agitation → Solution',
            'step-by-step' => 'Numbered step-by-step guide',
            'listicle' => 'Numbered list of tips or insights',
            'before-after' => 'Before state → Transformation → After state',
            'case-study' => 'Client situation → Challenge → Action → Result',
            default => 'Problem → Agitation → Solution',
        };

        return <<<PROMPT
BRAND: {$brand->name}
AUDIENCE: {$brand->target_audience}
TOPIC: {$topic}
PLATFORM: {$platform}
STRUCTURE: {$structureDesc}
SLIDE COUNT: {$slideRange}

Generate a complete carousel slide deck. Return this exact JSON:
{
  "platform": "{$platform}",
  "structure": "{$structure}",
  "total_slides": 8,
  "slides": [
    {
      "slide": 1,
      "type": "hook",
      "headline": "Bold hook headline (under 8 words)",
      "body": "",
      "visual_note": "Brief Canva design suggestion"
    },
    {
      "slide": 2,
      "type": "content",
      "headline": "Slide headline",
      "body": "1–3 short lines of copy",
      "visual_note": "Visual direction for this slide"
    },
    {
      "slide": 8,
      "type": "cta",
      "headline": "CTA headline",
      "body": "One clear next step",
      "visual_note": "Visual direction for CTA slide"
    }
  ],
  "canva_tip": "One practical tip for designing this carousel in Canva"
}

Respond with valid JSON only.
PROMPT;
    }

    private function buildQuoteSystemPrompt(Brand $brand): string
    {
        $voice = $brand->brand_voice
            ? 'Brand Voice: '.($brand->brand_voice['writing_summary'] ?? '')
            : '';

        return <<<PROMPT
You are a copywriter extracting shareable quotes and social proof cards for African B2B founders.
Brand: {$brand->name}
{$voice}
Extract or rewrite the most shareable, visual-ready copy for social media cards.
Return valid JSON only. No markdown.
PROMPT;
    }

    private function buildQuoteUserPrompt(string $input, string $cardType): string
    {
        return <<<PROMPT
INPUT TEXT:
{$input}

CARD TYPE REQUESTED: {$cardType}

Generate all three card types from this input. Return this exact JSON:
{
  "quote_card": {
    "quote": "The most shareable single sentence (founder quote, punchy and memorable)",
    "attribution": "Name or brand attribution line",
    "visual_note": "Background mood and colour suggestion for Canva"
  },
  "testimonial_card": {
    "quote": "Rewritten as a clean client testimonial (under 30 words)",
    "name": "Client or persona name (use generic if not provided)",
    "result": "One-line result or outcome",
    "visual_note": "Visual direction for testimonial card"
  },
  "motivational_card": {
    "quote": "Short motivational quote extracted or derived (under 12 words)",
    "visual_note": "Bold background mood and typography suggestion"
  }
}

Respond with valid JSON only.
PROMPT;
    }

    // ── Parsers ───────────────────────────────────────────────────────────────

    /**
     * @return array<string, mixed>
     */
    private function parse(string $raw): array
    {
        $clean = trim(preg_replace('/^```json\s*/m', '', preg_replace('/^```\s*/m', '', $raw)));
        $data = json_decode($clean, true);

        if (json_last_error() !== JSON_ERROR_NONE || ! is_array($data)) {
            return [
                'platform' => 'unknown',
                'structure' => 'unknown',
                'total_slides' => 1,
                'slides' => [['slide' => 1, 'type' => 'content', 'headline' => '', 'body' => $raw, 'visual_note' => '']],
                'canva_tip' => '',
            ];
        }

        return $data;
    }

    /**
     * @return array<string, mixed>
     */
    private function parseQuote(string $raw): array
    {
        $clean = trim(preg_replace('/^```json\s*/m', '', preg_replace('/^```\s*/m', '', $raw)));
        $data = json_decode($clean, true);

        if (json_last_error() !== JSON_ERROR_NONE || ! is_array($data)) {
            return [
                'quote_card' => ['quote' => $raw, 'attribution' => '', 'visual_note' => ''],
                'testimonial_card' => ['quote' => '', 'name' => '', 'result' => '', 'visual_note' => ''],
                'motivational_card' => ['quote' => '', 'visual_note' => ''],
            ];
        }

        return $data;
    }
}
