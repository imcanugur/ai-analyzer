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

    public function __construct()
    {
        $this->endpoint = config('ai.providers.ollama.endpoint', 'http://localhost:11434');
        $this->defaultModel = config('ai.providers.ollama.default_model', 'gemma2');
        $this->timeout = (int) config('ai.providers.ollama.timeout', 60);
    }

    /**
     * Generate text completion using Ollama.
     */
    public function generate(string $prompt, array $options = []): AIResponse
    {
        $model = $options['model'] ?? $this->defaultModel;
        
        // Remove 'model' from options so it is not passed twice in the Ollama body options block
        unset($options['model']);

        $url = rtrim($this->endpoint, '/') . '/api/generate';

        $startTime = microtime(true);

        try {
            $response = Http::timeout($this->timeout)
                ->post($url, [
                    'model' => $model,
                    'prompt' => $prompt,
                    'stream' => false,
                    'options' => $options,
                ]);

            if ($response->failed()) {
                throw new \RuntimeException("Ollama request failed: " . $response->body());
            }

            $data = $response->json();
            $text = $data['response'] ?? '';
            
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
            Log::error("Ollama API Error: " . $e->getMessage());
            throw $e;
        }
    }
}
