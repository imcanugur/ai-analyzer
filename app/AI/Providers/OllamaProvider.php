<?php

namespace App\AI\Providers;

use App\AI\Contracts\AIProviderInterface;
use App\AI\DTO\AIResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OllamaProvider implements AIProviderInterface
{
    protected string $endpoint;

    protected string $defaultModel;

    protected int $timeout;

    protected ?string $apiKey;

    public function __construct()
    {
        $this->endpoint = config('ai.providers.ollama.endpoint', 'http://localhost:11434');
        $this->defaultModel = config('ai.providers.ollama.default_model', 'gemma2');
        $this->timeout = (int) config('ai.providers.ollama.timeout', 60);
        $this->apiKey = config('ai.providers.ollama.api_key') ?: null;
    }

    /**
     * Generate text completion using Ollama (local or cloud).
     */
    public function generate(string $prompt, array $options = [], ?string $systemPrompt = null): AIResponse
    {

        Log::error('OllamaProvider::generate çalışıyor.', [
            'prompt' => $prompt ?? 'prompt bulunamadı',
            'options' => $options ?? 'options bulunamadı',
            'systemPrompt' => $systemPrompt ?? 'systemPrompt bulunamadı',
            'endpoint' => $this->endpoint ?? 'endpoint bulunamadı',
            'defaultModel' => $this->defaultModel ?? 'defaultModel bulunamadı',
            'timeout' => $this->timeout ?? 'timeout bulunamadı',
            'apiKey' => $this->apiKey ?? 'apiKey bulunamadı',
            'model' => $model ?? 'model bulunamadı',
            'url' => $url ?? 'url bulunamadı',
            'body' => $body ?? 'body bulunamadı',
            'rawResponse' => $rawResponse ?? 'rawResponse bulunamadı',
            'tokens' => $tokens ?? 'tokens bulunamadı',

        ]);

        $model = $options['model'] ?? $this->defaultModel;

        unset($options['model']);

        $url = rtrim($this->endpoint, '/').'/api/generate';

        $startTime = microtime(true);

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

            // Only include options if non-empty, cast to object for JSON {} serialization
            if (! empty($options)) {
                $body['options'] = (object) $options;
            }

            $response = $http->post($url, $body);

            Log::error('OllamaProvider::generate çalışıyor.', [
                'prompt' => $prompt ?? 'prompt bulunamadı',
                'options' => $options ?? 'options bulunamadı',
                'systemPrompt' => $systemPrompt ?? 'systemPrompt bulunamadı',
                'endpoint' => $this->endpoint ?? 'endpoint bulunamadı',
                'defaultModel' => $this->defaultModel ?? 'defaultModel bulunamadı',
                'timeout' => $this->timeout ?? 'timeout bulunamadı',
                'apiKey' => $this->apiKey ?? 'apiKey bulunamadı',
                'model' => $model ?? 'model bulunamadı',
                'url' => $url ?? 'url bulunamadı',
                'body' => $body ?? 'body bulunamadı',

            ]);

            if ($response->failed()) {
                throw new \RuntimeException('Ollama request failed: '.$response->body());
            }

            $data = $response->json();
            $text = $data['response'] ?? '';

            Log::error('OllamaProvider::generate çalışıyor.', [
                'prompt' => $prompt ?? 'prompt bulunamadı',
                'options' => $options ?? 'options bulunamadı',
                'systemPrompt' => $systemPrompt ?? 'systemPrompt bulunamadı',
                'endpoint' => $this->endpoint ?? 'endpoint bulunamadı',
                'defaultModel' => $this->defaultModel ?? 'defaultModel bulunamadı',
                'timeout' => $this->timeout ?? 'timeout bulunamadı',
                'apiKey' => $this->apiKey ?? 'apiKey bulunamadı',
                'model' => $model ?? 'model bulunamadı',
                'url' => $url ?? 'url bulunamadı',
                'body' => $body ?? 'body bulunamadı',
                'data' => $data ?? 'data bulunamadı',
                'text' => $text ?? 'text bulunamadı',

            ]);

            // Calculate total tokens processed (prompt eval tokens + generation response tokens)
            $tokens = ($data['prompt_eval_count'] ?? 0) + ($data['eval_count'] ?? 0);

            $executionTime = (int) ((microtime(true) - $startTime) * 1000);

            return new AIResponse(
                text: $text,
                tokens: $tokens,
                executionTime: $executionTime,
                rawResponse: $data
            );

        } catch (\Exception $e) {
            Log::error('Ollama API Error: '.$e->getMessage());
            throw $e;
        }
    }
}
