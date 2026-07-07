<?php

namespace App\DTO;

use App\Models\Submission;

class StartAnalysisDTO
{
    public function __construct(
        public readonly Submission $submission,
        public readonly string $type,
        public readonly ?string $category = null,
        public readonly ?string $provider = null,
        public readonly ?string $engine = null,
        public readonly ?string $model = null,
        public readonly ?string $version = null,
        public readonly ?array $config = null,
        public readonly ?array $metadata = null
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            submission: $data['submission'],
            type: $data['type'],
            category: $data['category'] ?? null,
            provider: $data['provider'] ?? null,
            engine: $data['engine'] ?? null,
            model: $data['model'] ?? null,
            version: $data['version'] ?? null,
            config: $data['config'] ?? null,
            metadata: $data['metadata'] ?? null
        );
    }
}
