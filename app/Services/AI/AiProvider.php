<?php

namespace App\Services\Ai;

interface AiProvider
{
    /**
     * Generate content from a prompt.
     *
     * @param  string  $systemPrompt  Role + brand context
     * @param  string  $userPrompt  The actual generation request
     * @return string Raw text response
     *
     * @throws AiProviderException
     */
    public function generate(string $systemPrompt, string $userPrompt, int $maxTokens = 4096): string;

    /** Human-readable name for UI and logs. */
    public function name(): string;
}
