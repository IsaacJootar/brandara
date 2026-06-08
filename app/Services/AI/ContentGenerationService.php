<?php

namespace App\Services\Ai;

use App\Models\Brand;

class ContentGenerationService
{
    public function __construct(private readonly AiProviderFactory $factory) {}

    /**
     * Generate 3 post variations for the given input and platforms.
     *
     * Returns an array with keys: authority, story, bold.
     * Each key holds a map of platform → adapted content.
     *
     * @param  string[]  $platforms
     * @return array{authority: array, story: array, bold: array}
     *
     * @throws AiProviderException
     */
    public function generate(
        Brand $brand,
        string $inputType,
        string $input,
        array $platforms,
        string $tone,
    ): array {
        $provider = $this->factory->make();
        $system = $this->buildSystemPrompt($brand, $tone);
        $user = $this->buildUserPrompt($inputType, $input, $platforms);

        $raw = $provider->generate($system, $user);

        return $this->parseVariations($raw, $platforms);
    }

    // ── Prompts ───────────────────────────────────────────────────────────────

    private function buildSystemPrompt(Brand $brand, string $tone): string
    {
        $voiceDna = $brand->voice_dna
            ? "\n\nBrand Voice DNA:\n".json_encode($brand->voice_dna, JSON_PRETTY_PRINT)
            : '';

        $brandContext = implode("\n", array_filter([
            $brand->name ? "Brand name: {$brand->name}" : null,
            $brand->tagline ? "Tagline: {$brand->tagline}" : null,
            $brand->description ? "What they do: {$brand->description}" : null,
            $brand->target_audience ? "Target audience: {$brand->target_audience}" : null,
            $brand->positioning ? "Positioning: {$brand->positioning}" : null,
            $brand->negative_brief ? "Never say: {$brand->negative_brief}" : null,
        ]));

        $toneMap = [
            'professional' => 'polished and professional, credibility-focused',
            'founder' => 'raw founder voice — honest, direct, personal',
            'african' => 'warm, culturally resonant African business voice',
            'friendly' => 'conversational and approachable',
            'bold' => 'bold, provocative, attention-grabbing',
            'educational' => 'clear, educational, value-driven',
            'corporate' => 'formal corporate tone',
            'luxury' => 'premium, aspirational, exclusive',
        ];

        $toneDescription = $toneMap[$tone] ?? 'professional';

        return <<<PROMPT
You are an expert social media content strategist for African B2B businesses.
You write content that resonates with entrepreneurs and professionals in Nigeria, Ghana, Kenya, and South Africa.

{$brandContext}{$voiceDna}

Tone: {$toneDescription}

Rules:
- Write in plain, clear English. No jargon.
- Make every post feel native to its platform.
- Each variation must have a distinct angle — do not repeat ideas.
- Never use generic corporate filler like "synergy", "leverage", "game-changer".
- Always end posts with a question or call to action.
- Hashtags go at the end, never inline.
PROMPT;
    }

    private function buildUserPrompt(string $inputType, string $input, array $platforms): string
    {
        $platformInstructions = $this->platformInstructions($platforms);

        $inputContext = match ($inputType) {
            'topic' => "TOPIC: {$input}",
            'transcript' => "TRANSCRIPT/NOTES:\n{$input}",
            'product' => "PRODUCT/OFFER DESCRIPTION:\n{$input}",
            default => "DRAFT POST:\n{$input}",
        };

        return <<<PROMPT
Generate exactly 3 post variations from the following input.

{$inputContext}

---

For each variation, adapt the content for these platforms: {$platformInstructions}

Output format — follow this EXACTLY (JSON only, no extra text):

{
  "authority": {
    "angle": "Expert positioning — share insight or hard-won lesson",
    "platforms": {
      [platform_key]: {"body": "...", "hashtags": ["...", "..."]}
    }
  },
  "story": {
    "angle": "Narrative — a client result, personal journey, or turning point",
    "platforms": {
      [platform_key]: {"body": "...", "hashtags": ["...", "..."]}
    }
  },
  "bold": {
    "angle": "Strong opinion or contrarian take — provokes thought",
    "platforms": {
      [platform_key]: {"body": "...", "hashtags": ["...", "..."]}
    }
  }
}

Platform character limits to respect:
- linkedin: 3000 chars
- twitter: 280 chars
- facebook: 63206 chars
- instagram: 2200 chars (caption)
- threads: 500 chars
- whatsapp: 4096 chars
- tiktok: 2200 chars (caption)

Respond with valid JSON only. No markdown code blocks. No extra explanation.
PROMPT;
    }

    private function platformInstructions(array $platforms): string
    {
        $notes = [
            'linkedin' => 'LinkedIn (professional, paragraphs, 1-3 hashtags)',
            'twitter' => 'X/Twitter (punchy, under 280 chars, max 2 hashtags)',
            'facebook' => 'Facebook (conversational, can be longer)',
            'instagram' => 'Instagram (visual hook first, emoji-friendly, 5-10 hashtags)',
            'threads' => 'Threads (casual, short, no hashtags or max 2)',
            'whatsapp' => 'WhatsApp (broadcast-style, warm, no hashtags)',
            'tiktok' => 'TikTok (caption only, hook in first line, 3-5 hashtags)',
        ];

        return implode('; ', array_map(
            fn ($p) => $notes[$p] ?? $p,
            array_intersect(array_keys($notes), $platforms)
        ));
    }

    // ── Parser ────────────────────────────────────────────────────────────────

    private function parseVariations(string $raw, array $platforms): array
    {
        // Strip markdown code fences if the model added them
        $clean = preg_replace('/^```json\s*/m', '', $raw);
        $clean = preg_replace('/^```\s*/m', '', $clean);
        $clean = trim($clean);

        $data = json_decode($clean, true);

        if (json_last_error() !== JSON_ERROR_NONE || ! is_array($data)) {
            // Fallback: wrap raw text as a single variation
            $fallback = ['body' => $raw, 'hashtags' => []];
            $platformFallback = array_fill_keys($platforms, $fallback);

            return [
                'authority' => ['angle' => 'Generated content', 'platforms' => $platformFallback],
                'story' => ['angle' => 'Generated content', 'platforms' => $platformFallback],
                'bold' => ['angle' => 'Generated content', 'platforms' => $platformFallback],
            ];
        }

        // Ensure all requested platforms exist in output
        foreach (['authority', 'story', 'bold'] as $variation) {
            if (! isset($data[$variation]['platforms'])) {
                $data[$variation]['platforms'] = [];
            }
            foreach ($platforms as $platform) {
                if (! isset($data[$variation]['platforms'][$platform])) {
                    $data[$variation]['platforms'][$platform] = ['body' => '', 'hashtags' => []];
                }
            }
        }

        return $data;
    }
}
