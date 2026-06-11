<?php

namespace App\Services\AiVisibility;

use Anthropic\Anthropic;
use App\Models\AiPresenceResult;
use App\Models\Brand;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Queries AI platforms to check if the brand appears in answers.
 *
 * Active: claude (if key set), chatgpt (if key set), gemini (if key set)
 * Coming soon: perplexity
 *
 * Provider toggles managed in admin (Module 22). Add a new provider by
 * implementing its query method — nothing else changes.
 */
class AiPresenceService
{
    public function runAll(Brand $brand): array
    {
        $prompts = $this->buildPrompts($brand);
        $results = [];

        foreach ($prompts as $prompt) {
            if (config('services.anthropic.key')) {
                $r = $this->queryClaude($brand, $prompt);
                if ($r) {
                    $results[] = $r;
                }
            }
            if (config('services.openai.key')) {
                $r = $this->queryChatGpt($brand, $prompt);
                if ($r) {
                    $results[] = $r;
                }
            }
            if (config('services.gemini.key')) {
                $r = $this->queryGemini($brand, $prompt);
                if ($r) {
                    $results[] = $r;
                }
            }
        }

        return $results;
    }

    public function runProvider(Brand $brand, string $provider): array
    {
        $prompts = $this->buildPrompts($brand);
        $results = [];
        foreach ($prompts as $prompt) {
            $r = match ($provider) {
                'claude' => $this->queryClaude($brand, $prompt),
                'chatgpt' => $this->queryChatGpt($brand, $prompt),
                'gemini' => $this->queryGemini($brand, $prompt),
                default => null,
            };
            if ($r) {
                $results[] = $r;
            }
        }

        return $results;
    }

    public function buildPrompts(Brand $brand): array
    {
        $country = $brand->workspace?->country ?? 'NG';
        $location = match ($country) {
            'NG' => 'Nigeria', 'GH' => 'Ghana', 'KE' => 'Kenya', 'ZA' => 'South Africa', default => 'Africa',
        };
        $industry = $this->extractIndustry($brand);
        $name = $brand->name;

        return [
            ['text' => "Best {$industry} in {$location}",                               'category' => 'discovery'],
            ['text' => "Top {$industry} businesses in {$location}",                      'category' => 'discovery'],
            ['text' => "Who is {$name} and what do they do?",                            'category' => 'trust'],
            ['text' => "Recommended {$industry} for businesses in {$location}",          'category' => 'local_intent'],
            ['text' => "What should I look for when choosing a {$industry} in {$location}?", 'category' => 'consideration'],
            ['text' => "Is {$name} a reliable {$industry}?",                             'category' => 'trust'],
        ];
    }

    private function queryClaude(Brand $brand, array $prompt): ?AiPresenceResult
    {
        try {
            $client = new Anthropic(['api_key' => config('services.anthropic.key')]);
            $response = $client->messages()->create([
                'model' => 'claude-haiku-4-5',
                'max_tokens' => 400,
                'system' => 'You are a helpful assistant. Answer concisely and honestly.',
                'messages' => [['role' => 'user', 'content' => $prompt['text']]],
            ]);

            return $this->parseAndStore($brand, 'claude', $prompt, trim($response->content[0]->text ?? ''));
        } catch (\Throwable $e) {
            Log::warning('AiPresenceService: Claude failed', ['error' => $e->getMessage()]);

            return null;
        }
    }

    private function queryChatGpt(Brand $brand, array $prompt): ?AiPresenceResult
    {
        try {
            $response = Http::withToken(config('services.openai.key'))->timeout(20)
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => 'gpt-4o-mini', 'max_tokens' => 400,
                    'messages' => [
                        ['role' => 'system', 'content' => 'Answer concisely and honestly.'],
                        ['role' => 'user', 'content' => $prompt['text']],
                    ],
                ]);

            return $this->parseAndStore($brand, 'chatgpt', $prompt, $response->json('choices.0.message.content', ''));
        } catch (\Throwable $e) {
            Log::warning('AiPresenceService: ChatGPT failed', ['error' => $e->getMessage()]);

            return null;
        }
    }

    private function queryGemini(Brand $brand, array $prompt): ?AiPresenceResult
    {
        try {
            $key = config('services.gemini.key');
            $response = Http::timeout(20)
                ->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key={$key}", [
                    'contents' => [['parts' => [['text' => $prompt['text']]]]],
                    'generationConfig' => ['maxOutputTokens' => 400],
                ]);

            return $this->parseAndStore($brand, 'gemini', $prompt, $response->json('candidates.0.content.parts.0.text', ''));
        } catch (\Throwable $e) {
            Log::warning('AiPresenceService: Gemini failed', ['error' => $e->getMessage()]);

            return null;
        }
    }

    private function parseAndStore(Brand $brand, string $provider, array $prompt, string $raw): AiPresenceResult
    {
        $lower = strtolower($raw);
        $brandName = strtolower($brand->name);
        $appeared = str_contains($lower, $brandName);

        return AiPresenceResult::create([
            'brand_id' => $brand->id,
            'provider' => $provider,
            'prompt' => $prompt['text'],
            'prompt_category' => $prompt['category'],
            'appeared' => $appeared,
            'position' => $appeared ? $this->detectPosition($lower, $brandName) : null,
            'sentiment' => $appeared ? $this->detectSentiment($lower, $brandName) : 'not_mentioned',
            'raw_response' => $raw,
            'competitors_mentioned' => $this->detectCompetitors($raw, $brand),
            'queried_at' => now(),
        ]);
    }

    private function detectPosition(string $text, string $name): int
    {
        $lines = preg_split('/\n|\d+\.\s/', $text);
        foreach ($lines as $i => $line) {
            if (str_contains(strtolower($line), $name)) {
                return $i + 1;
            }
        }

        return 1;
    }

    private function detectSentiment(string $text, string $name): string
    {
        $positive = ['recommended', 'excellent', 'best', 'top', 'trusted', 'reliable', 'leading', 'great', 'reputable'];
        $negative = ['avoid', 'poor', 'bad', 'unreliable', 'complaints', 'issues'];
        foreach (preg_split('/(?<=[.!?])\s+/', $text) as $sentence) {
            if (! str_contains(strtolower($sentence), $name)) {
                continue;
            }
            $s = strtolower($sentence);
            foreach ($positive as $w) {
                if (str_contains($s, $w)) {
                    return 'positive';
                }
            }
            foreach ($negative as $w) {
                if (str_contains($s, $w)) {
                    return 'negative';
                }
            }
        }

        return 'neutral';
    }

    private function detectCompetitors(string $text, Brand $brand): array
    {
        preg_match_all('/\b([A-Z][a-z]+(?:\s+[A-Z][a-z]+)*)\b/', $text, $m);
        $stop = ['The', 'This', 'That', 'They', 'Here', 'Also', 'When', 'How', 'What', 'Who'];

        return array_values(array_filter(array_unique($m[1] ?? []),
            fn ($w) => strtolower($w) !== strtolower($brand->name) && ! in_array($w, $stop)));
    }

    private function extractIndustry(Brand $brand): string
    {
        $text = $brand->tagline ?? $brand->description ?? 'business';
        $words = array_values(array_filter(explode(' ', strtolower($text)), fn ($w) => strlen($w) > 4));

        return $words[0] ?? 'business';
    }

    public function presenceSummary(Brand $brand): array
    {
        $results = AiPresenceResult::where('brand_id', $brand->id)->latest('queried_at')->get();
        if ($results->isEmpty()) {
            return ['has_data' => false];
        }

        $total = $results->count();
        $appeared = $results->where('appeared', true)->count();

        return [
            'has_data' => true,
            'total' => $total,
            'appeared' => $appeared,
            'score' => $total > 0 ? (int) round($appeared / $total * 100) : 0,
            'by_provider' => $results->groupBy('provider')->map(fn ($g) => [
                'total' => $g->count(),
                'appeared' => $g->where('appeared', true)->count(),
                'score' => $g->count() > 0 ? (int) round($g->where('appeared', true)->count() / $g->count() * 100) : 0,
            ]),
            'last_queried' => $results->max('queried_at'),
            'results' => $results->take(12),
        ];
    }

    public function activeProviders(): array
    {
        $p = [];
        if (config('services.anthropic.key')) {
            $p[] = 'claude';
        }
        if (config('services.openai.key')) {
            $p[] = 'chatgpt';
        }
        if (config('services.gemini.key')) {
            $p[] = 'gemini';
        }

        return $p;
    }
}
