<?php

declare(strict_types=1);

namespace App\Services\Extractors;

use App\Contracts\TextExtractorInterface;
use Illuminate\Support\Facades\Log;
use ZipArchive;

class DocxExtractor implements TextExtractorInterface
{
    public function supports(string $mimeType, string $extension): bool
    {
        return strtolower($extension) === 'docx' ||
               str_contains($mimeType, 'wordprocessingml') ||
               $mimeType === 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
    }

    public function extract(string $fileContents, string $extension): string
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'docx_extract_');

        if ($tempFile === false) {
            return '';
        }

        try {
            file_put_contents($tempFile, $fileContents);

            $zip = new ZipArchive;
            if ($zip->open($tempFile) === true) {
                $xmlContent = $zip->getFromName('word/document.xml');
                $zip->close();

                if ($xmlContent !== false) {
                    return $this->parseDocxXml($xmlContent);
                }
            }
        } catch (\Throwable $e) {
            Log::error('[DocxExtractor] Failed to extract text from DOCX document.', ['error' => $e->getMessage()]);
        } finally {
            if (file_exists($tempFile)) {
                @unlink($tempFile);
            }
        }

        return '';
    }

    /**
     * Parse word/document.xml to extract clean paragraph text.
     */
    protected function parseDocxXml(string $xmlContent): string
    {
        $dom = new \DOMDocument;
        // Suppress XML warnings for custom Word tags
        libxml_use_internal_errors(true);
        $dom->loadXML($xmlContent);
        libxml_clear_errors();

        $paragraphs = [];
        $pNodes = $dom->getElementsByTagName('p');

        foreach ($pNodes as $pNode) {
            $pText = '';
            $tNodes = $pNode->getElementsByTagName('t');
            foreach ($tNodes as $tNode) {
                $pText .= $tNode->nodeValue;
            }

            if (! empty(trim($pText))) {
                $paragraphs[] = trim($pText);
            }
        }

        return implode("\n\n", $paragraphs);
    }
}
