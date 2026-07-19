<?php

declare(strict_types=1);

namespace App\Services\Extractors;

use App\Contracts\TextExtractorInterface;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;
use Smalot\PdfParser\Config as PdfConfig;
use Smalot\PdfParser\Parser;

class PdfExtractor implements TextExtractorInterface
{
    public function supports(string $mimeType, string $extension): bool
    {
        return strtolower($extension) === 'pdf' || $mimeType === 'application/pdf';
    }

    public function extract(string $fileContents, string $extension): string
    {
        // Tier 1: System CLI pdftotext
        $extractedText = $this->extractViaPdfToText($fileContents);
        if (mb_strlen(trim($extractedText)) > 30) {
            Log::info('[PdfExtractor] Text extracted successfully via pdftotext CLI.');
            return $extractedText;
        }

        // Tier 2: Smalot PdfParser PHP Library
        Log::info('[PdfExtractor] pdftotext CLI empty or short, trying Smalot PdfParser.');
        $extractedText = $this->extractViaSmalotParser($fileContents);
        if (mb_strlen(trim($extractedText)) > 30) {
            return $extractedText;
        }

        // Tier 3: OCR Fallback for Scanned PDFs
        Log::info('[PdfExtractor] Text extraction yields empty/short text. Executing Tier 3 OCR fallback.');

        return $this->extractViaOcrPpm($fileContents);
    }

    /**
     * Tier 1: System CLI pdftotext (Superior layout preservation & UTF-8 support).
     */
    protected function extractViaPdfToText(string $fileContents): string
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'pdf_extract_');

        if ($tempFile === false) {
            return '';
        }

        try {
            file_put_contents($tempFile, $fileContents);

            $result = Process::run(['pdftotext', '-enc', 'UTF-8', '-layout', $tempFile, '-']);

            if ($result->successful()) {
                return trim($result->output());
            }
        } catch (\Throwable $e) {
            Log::debug('[PdfExtractor] pdftotext command execution failed.', ['error' => $e->getMessage()]);
        } finally {
            if (file_exists($tempFile)) {
                @unlink($tempFile);
            }
        }

        return '';
    }

    /**
     * Tier 2: PHP-native Smalot PdfParser library.
     */
    protected function extractViaSmalotParser(string $fileContents): string
    {
        if (! class_exists(Parser::class)) {
            Log::warning('[PdfExtractor] Smalot PdfParser library not installed.');
            return '';
        }

        try {
            $config = new PdfConfig;
            $config->setFontSpaceLimit(config('pdf.font_space_limit', -15));

            $parser = new Parser([], $config);
            $pdf = $parser->parseContent($fileContents);

            return $pdf->getText();
        } catch (\Throwable $e) {
            Log::error('[PdfExtractor] Smalot PdfParser extraction failed.', ['error' => $e->getMessage()]);
            return '';
        }
    }

    /**
     * Tier 3: Convert PDF to PNG images via pdftoppm and run Tesseract OCR.
     */
    protected function extractViaOcrPpm(string $fileContents): string
    {
        $tempPdf = tempnam(sys_get_temp_dir(), 'pdf_ocr_');
        $outputPrefix = sys_get_temp_dir().'/page_'.uniqid();

        if ($tempPdf === false) {
            return '';
        }

        try {
            file_put_contents($tempPdf, $fileContents);

            // Convert first 5 pages to PNG using pdftoppm
            $result = Process::run(['pdftoppm', '-png', '-r', '150', '-f', '1', '-l', '5', $tempPdf, $outputPrefix]);

            if ($result->successful()) {
                $images = glob($outputPrefix.'-*.png');
                $ocrText = '';
                $ocrExtractor = app(OcrExtractor::class);

                foreach ($images as $imgFile) {
                    $imgContents = file_get_contents($imgFile);
                    $ocrText .= "\n\n".$ocrExtractor->extract($imgContents, 'png');
                    @unlink($imgFile);
                }

                return trim($ocrText);
            }
        } catch (\Throwable $e) {
            Log::warning('[PdfExtractor] OCR PPM conversion failed.', ['error' => $e->getMessage()]);
        } finally {
            if (file_exists($tempPdf)) {
                @unlink($tempPdf);
            }
        }

        return '';
    }
}
