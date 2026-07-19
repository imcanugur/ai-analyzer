<?php

declare(strict_types=1);

namespace App\Services\Extractors;

use App\Contracts\TextExtractorInterface;

class PlainTextExtractor implements TextExtractorInterface
{
    protected array $supportedExtensions = [
        'txt', 'md', 'json', 'xml', 'csv', 'sql', 'css', 'js', 'ts', 'py', 'php', 'html', 'yaml', 'yml', 'log',
    ];

    public function supports(string $mimeType, string $extension): bool
    {
        return str_starts_with($mimeType, 'text/') || in_array(strtolower($extension), $this->supportedExtensions, true);
    }

    public function extract(string $fileContents, string $extension): string
    {
        return $fileContents;
    }
}
