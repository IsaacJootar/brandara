<?php

namespace App\Services\Engagement;

use App\Models\Brand;
use App\Models\EngagementRule;
use App\Services\Ai\AiProviderException;
use App\Services\Ai\AiProviderFactory;

/**
 * Generates a genuine, contextual comment for a target post
 * using the brand's Brand Voice. Not a template — reads the
 * actual post content and writes a human-sounding response.
 */
class CommentGeneratorService
{
    public function __construct(private readonly AiProviderFactory $factory) {}

    /**
     * Generate a contextual comment for the given post excerpt.
     *
     * @throws AiProviderException
     */
    public function generate(Brand $brand, EngagementRule $rule, string $postExcerpt, string $postAuthor): string
    {
        $provider = $this->factory->make();

        $system = $this->buildSystemPrompt($brand, $rule);
        $user = $this->buildUserPrompt($postExcerpt, $postAuthor, $rule->platform);

        $raw = $provider->generate($system, $user, 512);

        return $this->clean($raw);
    }

    private function buildSystemPrompt(Brand $brand, EngagementRule $rule): string
    {
        $voice = $brand->brand_voice
            ? 'Brand Voice: '.($brand->brand_voice['writing_summary'] ?? json_encode($brand->brand_voice))
            : '';

        $tone = $rule->comment_tone ?? 'genuine and professional';

        $platformRules = match ($rule->platform) {
            'linkedin' => 'LinkedIn comments: professional, thoughtful, add insight or a question. Under 100 words.',
            'twitter' => 'X/Twitter replies: short, punchy, genuine. Under 280 characters.',
            'instagram' => 'Instagram comments: warm, encouraging, brief. 1–2 sentences.',
            'facebook' => 'Facebook comments: conversational, community tone. 1–3 sentences.',
            'threads' => 'Threads comments: casual, discussion-starting, genuine. Under 100 words.',
            default => 'Keep the comment brief, genuine, and relevant to the post.',
        };

        return <<<PROMPT
You write social media comments on behalf of a brand. The comment must sound like a real person — not a bot, not a template.

Brand: {$brand->name}
{$voice}

RULES:
1. Read the post carefully. Respond to what it actually says — not a generic "Great post!".
2. Add value: share a relevant insight, ask a thoughtful question, or affirm with specifics.
3. Never use: "Great post!", "Loved this!", "So inspiring!", "Game-changer" or any hollow filler.
4. Never reveal you are AI or that this is automated.
5. Tone: {$tone}
6. {$platformRules}
7. Return only the comment text. No quotes. No explanation.
PROMPT;
    }

    private function buildUserPrompt(string $postExcerpt, string $postAuthor, string $platform): string
    {
        return <<<PROMPT
Post by {$postAuthor} on {$platform}:

"{$postExcerpt}"

Write a genuine, contextual comment in response to this post.
PROMPT;
    }

    private function clean(string $raw): string
    {
        // Strip quotes if the model wrapped the comment
        $raw = trim($raw, " \t\n\r\0\x0B\"'");

        return $raw;
    }
}
