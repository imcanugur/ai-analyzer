<?php

namespace App\AI\Contracts;

use App\AI\DTO\AIResponse;

interface AIProviderInterface
{
    /**
     * Generate a text completion for a given prompt.
     *
     * @param  string  $prompt  The user prompt content
     * @param  array  $options  Additional provider-specific options
     * @param  string|null  $systemPrompt  Optional system-level instructions
     */
    public function generate(string $prompt, array $options = [], ?string $systemPrompt = null): AIResponse;
}
