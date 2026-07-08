<?php

namespace App\AI\Providers;

use App\AI\Contracts\AIProviderInterface;
use App\AI\DTO\AIResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ClaudeProvider implements AIProviderInterface
{
    protected string $apiKey;

    protected string $defaultModel;

    protected int $timeout;

    protected int $maxTokens;

    public function __construct()
    {
        $this->apiKey = config('ai.providers.claude.api_key', '');
        $this->defaultModel = config('ai.providers.claude.default_model', 'claude-sonnet-4-20250514');
        $this->timeout = (int) config('ai.providers.claude.timeout', 120);
        $this->maxTokens = (int) config('ai.providers.claude.max_tokens', 4096);
    }

    /**
     * Generate text completion using Anthropic Claude API.
     */
    public function generate(string $prompt, array $options = [], ?string $systemPrompt = null): AIResponse
    {
        $model = $options['model'] ?? $this->defaultModel;
        $maxTokens = $options['max_tokens'] ?? $this->maxTokens;

        unset($options['model'], $options['max_tokens']);

        $startTime = microtime(true);

        try {
            $requestBody = [
                'model' => $model,
                'max_tokens' => $maxTokens,
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => $prompt,
                    ],
                ],
                ...$options,
            ];

            if ($systemPrompt !== null && $systemPrompt !== '') {
                $requestBody['system'] = $systemPrompt;
            }

            $response = Http::timeout($this->timeout)
                ->withHeaders([
                    'x-api-key' => $this->apiKey,
                    'anthropic-version' => '2023-06-01',
                    'content-type' => 'application/json',
                ])
                ->post('https://api.anthropic.com/v1/messages', $requestBody);

            if ($response->failed()) {
                throw new \RuntimeException('Claude request failed: '.$response->body());
            }

            $data = $response->json();

            // Extract text from content blocks
            $text = collect($data['content'] ?? [])
                ->where('type', 'text')
                ->pluck('text')
                ->implode("\n");

            // Token usage from Anthropic API
            $inputTokens = $data['usage']['input_tokens'] ?? 0;
            $outputTokens = $data['usage']['output_tokens'] ?? 0;
            $tokens = $inputTokens + $outputTokens;

            $executionTime = (int) ((microtime(true) - $startTime) * 1000);

            return new AIResponse(
                text: $text,
                tokens: $tokens,
                executionTime: $executionTime,
                rawResponse: $data
            );

        } catch (\Exception $e) {
            Log::error('Claude API Error: '.$e->getMessage());
            throw $e;
        }
    }
}
