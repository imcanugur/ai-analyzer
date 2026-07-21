<?php

declare(strict_types=1);

namespace App\AI\Providers;

use App\AI\Contracts\AIProviderInterface;
use App\AI\DTO\AIResponse;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class OllamaProvider implements AIProviderInterface
{
    protected string $endpoint;

    protected string $defaultModel;

    protected int $timeout;

    protected ?string $apiKey;

    public function __construct(
        ?string $endpoint = null,
        ?string $defaultModel = null,
        ?int $timeout = null,
        ?string $apiKey = null
    ) {
        $this->endpoint = $endpoint ?? config('ai.providers.ollama.endpoint', 'http://localhost:11434');
        $this->defaultModel = $defaultModel ?? config('ai.providers.ollama.default_model', 'gemma2');
        $this->timeout = $timeout ?? (int) config('ai.providers.ollama.timeout', 300);
        $this->apiKey = $apiKey ?? config('ai.providers.ollama.api_key') ?: null;
    }

    /**
     * Generate text completion using Ollama (local or cloud).
     */
    public function generate(string $prompt, array $options = [], ?string $systemPrompt = null): AIResponse
    {
        $model = $options['model'] ?? $this->defaultModel;

        $url = rtrim($this->endpoint, '/').'/api/generate';
        $startTime = microtime(true);

        Log::info('[OllamaProvider] API request initiated.', [
            'model' => $model,
            'endpoint' => $url,
            'prompt_length' => mb_strlen($prompt),
            'system_prompt_length' => $systemPrompt !== null ? mb_strlen($systemPrompt) : 0,
        ]);

        try {
            $http = Http::timeout($this->timeout);

            // If API key is set, add Bearer token auth (cloud mode)
            if ($this->apiKey) {
                $http = $http->withHeaders([
                    'Authorization' => 'Bearer '.$this->apiKey,
                ]);
            }

            $body = [
                'model' => $model,
                'prompt' => $prompt,
                'stream' => false,
                'format' => 'json',
            ];

            if ($systemPrompt !== null && $systemPrompt !== '') {
                $body['system'] = $systemPrompt;
            }

            // Map and sanitize Ollama API options strictly
            $ollamaOptions = [];
            if (isset($options['temperature'])) {
                $ollamaOptions['temperature'] = (float) $options['temperature'];
            }
            if (isset($options['max_tokens'])) {
                $ollamaOptions['num_predict'] = (int) $options['max_tokens'];
            } elseif (isset($options['num_predict'])) {
                $ollamaOptions['num_predict'] = (int) $options['num_predict'];
            }
            if (isset($options['top_p'])) {
                $ollamaOptions['top_p'] = (float) $options['top_p'];
            }
            if (isset($options['top_k'])) {
                $ollamaOptions['top_k'] = (int) $options['top_k'];
            }

            if (! empty($ollamaOptions)) {
                $body['options'] = (object) $ollamaOptions;
            }

            $response = $http->post($url, $body);

            if ($response->failed()) {
                throw new RuntimeException('Ollama request failed: '.$response->body());
            }

            $data = $response->json();
            $text = trim($data['response'] ?? '');

            // Calculate total tokens processed (prompt eval count + eval count)
            $tokens = ($data['prompt_eval_count'] ?? 0) + ($data['eval_count'] ?? 0);
            $executionTime = (int) ((microtime(true) - $startTime) * 1000);

            Log::info('[OllamaProvider] API request completed successfully.', [
                'execution_time_ms' => $executionTime,
                'tokens_processed' => $tokens,
                'response_length' => mb_strlen($text),
            ]);

            return new AIResponse(
                text: $text,
                tokens: $tokens,
                executionTime: $executionTime,
                rawResponse: $data
            );

        } catch (Exception $e) {
            Log::error('[OllamaProvider] API request failed.', [
                'error' => $e->getMessage(),
                'model' => $model,
                'endpoint' => $url,
            ]);
            throw $e;
        }
    }
}
