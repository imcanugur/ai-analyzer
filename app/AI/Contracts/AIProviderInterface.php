<?php

namespace App\AI\Contracts;

use App\AI\DTO\AIResponse;

interface AIProviderInterface
{
    /**
     * Generate a text completion for a given prompt.
     *
     * @param string $prompt
     * @param array $options
     * @return AIResponse
     */
    public function generate(string $prompt, array $options = []): AIResponse;
}
