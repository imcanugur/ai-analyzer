<?php

namespace App\AI\DTO;

class AIResponse
{
    public function __construct(
        public readonly string $text,
        public readonly int $tokens,
        public readonly int $executionTime, // in milliseconds
        public readonly array $rawResponse = [],
        public readonly array $metadata = []
    ) {}
}
