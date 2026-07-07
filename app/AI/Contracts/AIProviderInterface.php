<?php

namespace App\AI\Contracts;

use App\AI\DTO\AIResponse;

interface AIProviderInterface
{
    /**
     * Generate a text completion for a given prompt.
     */
    public function generate(string $prompt, array $options = []): AIResponse;
}
