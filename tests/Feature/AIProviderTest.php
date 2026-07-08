<?php

namespace Tests\Feature;

use App\AI\Contracts\AIProviderInterface;
use App\AI\DTO\AIResponse;
use App\AI\Providers\OllamaProvider;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AIProviderTest extends TestCase
{
    /**
     * Test that AIProviderInterface resolves to OllamaProvider from the container.
     */
    public function test_container_resolves_to_ollama_provider(): void
    {
        $provider = app(AIProviderInterface::class);

        $this->assertInstanceOf(OllamaProvider::class, $provider);
    }

    /**
     * Test that OllamaProvider sends a valid HTTP request and parses the response.
     */
    public function test_ollama_provider_generates_response_successfully(): void
    {
        app()->forgetInstance(AIProviderInterface::class);

        Http::fake([
            '*/api/generate' => Http::response([
                'model' => 'gemma2',
                'response' => 'This is a test completion response from Gemma.',
                'prompt_eval_count' => 12,
                'eval_count' => 38,
            ], 200),
        ]);

        /** @var AIProviderInterface $provider */
        $provider = app(AIProviderInterface::class);

        $response = $provider->generate('Explain quantum computing in one sentence.', [
            'temperature' => 0.5,
        ]);

        // Assert DTO properties
        $this->assertInstanceOf(AIResponse::class, $response);
        $this->assertEquals('This is a test completion response from Gemma.', $response->text);
        $this->assertEquals(50, $response->tokens); // 12 + 38
        $this->assertGreaterThanOrEqual(0, $response->executionTime);
        $this->assertNotEmpty($response->rawResponse);

        // Verify a request was sent to Ollama
        Http::assertSentCount(1);
    }

    /**
     * Test that OllamaProvider handles connection failures/errors properly.
     */
    public function test_ollama_provider_throws_exception_on_failure(): void
    {
        Http::fake([
            '*/api/generate' => Http::response('Internal Server Error', 500),
        ]);

        /** @var AIProviderInterface $provider */
        $provider = app(AIProviderInterface::class);

        $this->expectException(\RuntimeException::class);

        $provider->generate('Hello');
    }

    /**
     * Test that AIProviderInterface resolves to ClaudeProvider when configured.
     */
    public function test_container_resolves_to_claude_provider(): void
    {
        config(['ai.default' => 'claude']);

        // Clear the singleton so it re-resolves with the new config
        app()->forgetInstance(AIProviderInterface::class);

        $provider = app(AIProviderInterface::class);

        $this->assertInstanceOf(\App\AI\Providers\ClaudeProvider::class, $provider);
    }

    /**
     * Test that ClaudeProvider sends a valid HTTP request and parses the response.
     */
    public function test_claude_provider_generates_response_successfully(): void
    {
        config([
            'ai.default' => 'claude',
            'ai.providers.claude.api_key' => 'test-api-key-123',
        ]);

        app()->forgetInstance(AIProviderInterface::class);

        Http::fake([
            'api.anthropic.com/v1/messages' => Http::response([
                'id' => 'msg_test123',
                'type' => 'message',
                'role' => 'assistant',
                'content' => [
                    ['type' => 'text', 'text' => 'Claude test response.'],
                ],
                'model' => 'claude-sonnet-4-20250514',
                'usage' => [
                    'input_tokens' => 15,
                    'output_tokens' => 25,
                ],
            ], 200),
        ]);

        /** @var AIProviderInterface $provider */
        $provider = app(AIProviderInterface::class);

        $response = $provider->generate('Explain quantum computing.');

        // Assert DTO properties
        $this->assertInstanceOf(AIResponse::class, $response);
        $this->assertEquals('Claude test response.', $response->text);
        $this->assertEquals(40, $response->tokens); // 15 + 25
        $this->assertGreaterThanOrEqual(0, $response->executionTime);

        // Verify request payload and auth header
        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'api.anthropic.com/v1/messages') &&
                $request->hasHeader('x-api-key', 'test-api-key-123') &&
                $request->hasHeader('anthropic-version', '2023-06-01') &&
                $request['model'] === 'claude-sonnet-4-20250514' &&
                $request['messages'][0]['content'] === 'Explain quantum computing.';
        });
    }

    /**
     * Test that ClaudeProvider handles API failures properly.
     */
    public function test_claude_provider_throws_exception_on_failure(): void
    {
        config([
            'ai.default' => 'claude',
            'ai.providers.claude.api_key' => 'test-key',
        ]);

        app()->forgetInstance(AIProviderInterface::class);

        Http::fake([
            'api.anthropic.com/v1/messages' => Http::response([
                'type' => 'error',
                'error' => ['type' => 'authentication_error', 'message' => 'Invalid API Key'],
            ], 401),
        ]);

        /** @var AIProviderInterface $provider */
        $provider = app(AIProviderInterface::class);

        $this->expectException(\RuntimeException::class);

        $provider->generate('Hello');
    }
}
