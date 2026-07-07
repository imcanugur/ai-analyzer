<?php

namespace App\DTO;

use Illuminate\Http\UploadedFile;

class CreateSubmissionDTO
{
    public function __construct(
        public readonly string $userId,
        public readonly string $title,
        public readonly ?string $description,
        public readonly UploadedFile|string $file,
        public readonly ?array $metadata = null
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            userId: $data['user_id'],
            title: $data['title'],
            description: $data['description'] ?? null,
            file: $data['file'],
            metadata: $data['metadata'] ?? null
        );
    }
}
