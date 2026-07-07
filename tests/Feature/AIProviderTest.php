<?php

namespace Tests\Feature;

use App\AI\Contracts\AIProviderInterface;
use App\AI\Providers\OllamaProvider;
use App\AI\DTO\AIResponse;
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
        $this->assertGreaterThan(0, $response->executionTime);
        $this->assertNotEmpty($response->rawResponse);

        // Verify request payload
        Http::assertSent(function ($request) {
            return str_contains($request->url(), '/api/generate') &&
                $request['model'] === 'gemma2' &&
                $request['prompt'] === 'Explain quantum computing in one sentence.' &&
                $request['stream'] === false &&
                $request['options'] === ['temperature' => 0.5];
        });
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
}
