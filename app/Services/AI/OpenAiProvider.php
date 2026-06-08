<?php

namespace App\Services\Ai;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class OpenAiProvider implements AiProvider
{
    private string $model;

    private Client $http;

    public function __construct()
    {
        $this->model = config('ai.openai.model', 'gpt-4o');
        $this->http = new Client([
            'base_uri' => 'https://api.openai.com/v1/',
            'timeout' => 60,
        ]);
    }

    public function name(): string
    {
        return 'OpenAI ('.$this->model.')';
    }

    public function generate(string $systemPrompt, string $userPrompt, int $maxTokens = 4096): string
    {
        $apiKey = config('ai.openai.api_key');

        if (empty($apiKey)) {
            throw new AiProviderException(
                'OpenAI API key is not configured. Add OPENAI_API_KEY to your .env file.',
                $this->name(),
                isConfigError: true,
            );
        }

        try {
            $response = $this->http->post('chat/completions', [
                'headers' => [
                    'Authorization' => 'Bearer '.$apiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'model' => $this->model,
                    'max_tokens' => $maxTokens,
                    'messages' => [
                        ['role' => 'system', 'content' => $systemPrompt],
                        ['role' => 'user', 'content' => $userPrompt],
                    ],
                ],
            ]);

            $body = json_decode((string) $response->getBody(), true);

            return $body['choices'][0]['message']['content'] ?? '';
        } catch (ClientException $e) {
            $status = $e->getResponse()->getStatusCode();
            $message = $status === 401
                ? 'OpenAI API key is invalid. Please check your OPENAI_API_KEY.'
                : 'OpenAI could not generate content right now. Please try again.';

            throw new AiProviderException($message, $this->name());
        } catch (AiProviderException $e) {
            throw $e;
        } catch (\Throwable $e) {
            throw new AiProviderException(
                'OpenAI could not generate content right now. Please try again in a moment.',
                $this->name(),
            );
        }
    }
}
