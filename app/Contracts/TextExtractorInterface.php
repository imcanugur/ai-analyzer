<?php

declare(strict_types=1);

namespace App\Contracts;

interface TextExtractorInterface
{
    /**
     * Determine if this extractor supports the given MIME type or file extension.
     */
    public function supports(string $mimeType, string $extension): bool;

    /**
     * Extract raw text content from the given file binary stream or path.
     */
    public function extract(string $fileContents, string $extension): string;
}
