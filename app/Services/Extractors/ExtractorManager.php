<?php

declare(strict_types=1);

namespace App\Services\Extractors;

use App\Contracts\TextExtractorInterface;

class ExtractorManager
{
    /**
     * @var array<int, TextExtractorInterface>
     */
    protected array $extractors = [];

    public function __construct(
        PlainTextExtractor $plainTextExtractor,
        PdfExtractor $pdfExtractor,
        DocxExtractor $docxExtractor,
        OcrExtractor $ocrExtractor
    ) {
        $this->extractors = [
            $pdfExtractor,
            $docxExtractor,
            $ocrExtractor,
            $plainTextExtractor,
        ];
    }

    /**
     * Resolve the appropriate extractor strategy for a given file.
     */
    public function resolve(string $mimeType, string $extension): TextExtractorInterface
    {
        foreach ($this->extractors as $extractor) {
            if ($extractor->supports($mimeType, $extension)) {
                return $extractor;
            }
        }

        // Default to PlainTextExtractor if no specialized extractor matched
        return new PlainTextExtractor;
    }

    /**
     * Extract text from raw file content and sanitize it via TextNormalizer.
     */
    public function extractAndNormalize(string $fileContents, string $mimeType, string $extension): string
    {
        $extractor = $this->resolve($mimeType, $extension);
        $rawText = $extractor->extract($fileContents, $extension);

        return app(TextNormalizer::class)->normalize($rawText);
    }
}
