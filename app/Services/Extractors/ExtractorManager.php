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
        protected PlainTextExtractor $plainTextExtractor,
        protected PdfExtractor $pdfExtractor,
        protected DocxExtractor $docxExtractor,
        protected OcrExtractor $ocrExtractor,
        protected TextNormalizer $textNormalizer
    ) {
        $this->extractors = [
            $this->pdfExtractor,
            $this->docxExtractor,
            $this->ocrExtractor,
            $this->plainTextExtractor,
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

        return $this->plainTextExtractor;
    }

    /**
     * Extract text from raw file content and sanitize it via TextNormalizer.
     */
    public function extractAndNormalize(string $fileContents, string $mimeType, string $extension): string
    {
        $extractor = $this->resolve($mimeType, $extension);
        $rawText = $extractor->extract($fileContents, $extension);

        return $this->textNormalizer->normalize($rawText);
    }
}
