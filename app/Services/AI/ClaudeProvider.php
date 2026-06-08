<?php

namespace App\Services\Ai;

use Anthropic;

class ClaudeProvider implements AiProvider
{
    private string $model;

    public function __construct()
    {
        $this->model = config('ai.claude.model', 'claude-sonnet-4-5');
    }

    public function name(): string
    {
        return 'Claude ('.$this->model.')';
    }

    public function generate(string $systemPrompt, string $userPrompt, int $maxTokens = 4096): string
    {
        $apiKey = config('ai.claude.api_key');

        if (empty($apiKey)) {
            throw new AiProviderException(
                'Anthropic API key is not configured. Add ANTHROPIC_API_KEY to your .env file.',
                $this->name(),
                isConfigError: true,
            );
        }

        try {
            $client = Anthropic::client($apiKey);

            $response = $client->messages()->create([
                'model' => $this->model,
                'max_tokens' => $maxTokens,
                'system' => $systemPrompt,
                'messages' => [
                    ['role' => 'user', 'content' => $userPrompt],
                ],
            ]);

            return $response->content[0]->text ?? '';
        } catch (AiProviderException $e) {
            throw $e;
        } catch (\Throwable $e) {
            throw new AiProviderException(
                'Claude could not generate content right now. Please try again in a moment.',
                $this->name(),
            );
        }
    }
}
