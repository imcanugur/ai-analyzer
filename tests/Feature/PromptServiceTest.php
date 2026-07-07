<?php

namespace Tests\Feature;

use App\Services\PromptService;
use Tests\TestCase;

class PromptServiceTest extends TestCase
{
    /**
     * Test that PromptService can be successfully resolved.
     */
    public function test_prompt_service_resolves(): void
    {
        $service = app(PromptService::class);
        $this->assertInstanceOf(PromptService::class, $service);
    }

    /**
     * Test loading an existing template with placeholder replacements.
     */
    public function test_loads_existing_template_with_replacements(): void
    {
        /** @var PromptService $service */
        $service = app(PromptService::class);

        $prompt = $service->load('summary', [
            'text' => 'Laravel is a web application framework.',
        ]);

        $this->assertStringContainsString('Analyze the following text', $prompt);
        $this->assertStringContainsString('Laravel is a web application framework.', $prompt);
        $this->assertStringNotContainsString('{{ text }}', $prompt);
    }

    /**
     * Test that loading a non-existent template throws an exception.
     */
    public function test_throws_exception_for_non_existent_template(): void
    {
        /** @var PromptService $service */
        $service = app(PromptService::class);

        $this->expectException(\InvalidArgumentException::class);

        $service->load('non_existent_template_xyz');
    }
}
