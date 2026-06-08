<?php

namespace App\Services\Ai;

class AiProviderFactory
{
    /**
     * Resolve the active AI provider.
     *
     * In Module 23 (Admin Panel), this will read from a DB setting so admins
     * can switch providers from the UI without touching code.
     * For now it reads from config/ai.php → BRANDARA_AI_PROVIDER env.
     */
    public function make(): AiProvider
    {
        $provider = config('ai.default', 'claude');

        return match ($provider) {
            'openai' => new OpenAiProvider,
            default => new ClaudeProvider,   // 'claude' is the default — always
        };
    }
}
