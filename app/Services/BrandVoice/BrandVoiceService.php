<?php

namespace App\Services\BrandVoice;

use App\Models\Brand;
use App\Services\Ai\AiProviderException;
use App\Services\Ai\AiProviderFactory;

class BrandVoiceService
{
    public function __construct(private readonly AiProviderFactory $factory) {}

    /**
     * Analyse writing samples and extract a brand voice profile.
     * Stores the result on the brand and returns the profile array.
     *
     * @return array<string, mixed>
     *
     * @throws AiProviderException
     */
    public function train(Brand $brand, string $samples): array
    {
        $provider = $this->factory->make();

        $system = $this->systemPrompt();
        $user = $this->userPrompt($brand->name, $samples);

        $raw = $provider->generate($system, $user, 2048);
        $profile = $this->parse($raw);

        $brand->brand_voice = $profile;
        $brand->voice_samples_count = $this->countSamples($samples);
        $brand->save();

        return $profile;
    }

    // ── Prompts ───────────────────────────────────────────────────────────────

    private function systemPrompt(): string
    {
        return <<<'PROMPT'
You are a writing style analyst. Analyse the provided writing samples and
extract a precise voice profile that can be used to replicate this person's
writing style in future content.

Focus on:
- Sentence length and rhythm (short punchy sentences or longer flowing ones?)
- Vocabulary level and preferred words (casual vs formal, simple vs complex)
- Structural patterns (lists? questions? numbered points? paragraphs?)
- Tone characteristics (humour level, warmth, directness, confidence level)
- Opening patterns (how do they typically start posts or paragraphs?)
- Closing patterns (how do they end — with a question, statement, or CTA?)
- Emoji usage (never, occasional, frequent — and which types)
- Punctuation style (em-dashes? ellipses? short sentences?)
- Topics they naturally gravitate toward
- Phrases or expressions they repeat

OUTPUT: Return valid JSON only. No markdown fences, no explanation outside the JSON.
PROMPT;
    }

    private function userPrompt(string $brandName, string $samples): string
    {
        return <<<PROMPT
Analyse these writing samples from {$brandName}:

SAMPLES:
{$samples}

Return this exact JSON structure:
{
  "sentence_length": "short|medium|long|mixed",
  "sentence_rhythm": "description of rhythm pattern",
  "vocabulary_level": "simple|conversational|professional|formal",
  "preferred_words": ["word1", "word2", "word3"],
  "avoided_words": ["word1", "word2"],
  "structure_preference": "prose|lists|questions|mixed",
  "opening_style": "description of how they open posts",
  "closing_style": "description of how they close",
  "tone_characteristics": {
    "humour": "none|dry|light|frequent",
    "warmth": "cold|neutral|warm|very_warm",
    "directness": "indirect|balanced|direct|very_direct",
    "confidence": "tentative|neutral|confident|bold"
  },
  "emoji_usage": "none|rare|occasional|frequent",
  "punctuation_style": "description",
  "recurring_themes": ["theme1", "theme2"],
  "signature_phrases": ["phrase1", "phrase2"],
  "writing_summary": "2-3 sentence summary of their distinctive voice"
}
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
                'writing_summary' => $raw,
                'sentence_length' => 'mixed',
                'vocabulary_level' => 'conversational',
                'structure_preference' => 'prose',
                'emoji_usage' => 'none',
                'tone_characteristics' => [
                    'humour' => 'none',
                    'warmth' => 'neutral',
                    'directness' => 'balanced',
                    'confidence' => 'neutral',
                ],
            ];
        }

        return $data;
    }

    private function countSamples(string $samples): int
    {
        $blocks = array_filter(array_map('trim', preg_split('/\n{2,}/', $samples)));

        return count($blocks);
    }
}
