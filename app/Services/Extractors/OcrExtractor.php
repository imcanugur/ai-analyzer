<?php

declare(strict_types=1);

namespace App\Services\Extractors;

use App\Contracts\TextExtractorInterface;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;

class OcrExtractor implements TextExtractorInterface
{
    protected array $supportedExtensions = ['png', 'jpg', 'jpeg', 'tif', 'tiff', 'bmp', 'webp'];

    public function supports(string $mimeType, string $extension): bool
    {
        return str_starts_with($mimeType, 'image/') || in_array(strtolower($extension), $this->supportedExtensions, true);
    }

    public function extract(string $fileContents, string $extension): string
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'ocr_img_');

        if ($tempFile === false) {
            return '[Image Extraction: Unable to create temporary file.]';
        }

        $imageFile = $tempFile.'.'.$extension;

        try {
            file_put_contents($imageFile, $fileContents);

            // Execute Tesseract OCR CLI
            $result = Process::run(['tesseract', $imageFile, 'stdout', '-l', 'tur+eng']);

            if ($result->successful() && ! empty(trim($result->output()))) {
                Log::info('[OcrExtractor] OCR extraction successful via Tesseract.');
                return trim($result->output());
            }
        } catch (\Throwable $e) {
            Log::warning('[OcrExtractor] Tesseract OCR execution failed or not installed.', ['error' => $e->getMessage()]);
        } finally {
            if (file_exists($tempFile)) {
                @unlink($tempFile);
            }
            if (file_exists($imageFile)) {
                @unlink($imageFile);
            }
        }

        Log::info('[OcrExtractor] OCR returned empty text or Tesseract is not installed.');

        return '[Image Text Extraction: Unable to extract readable text from image file ('.$extension.'). Tesseract OCR may not be installed on host system or image contains no text.]';
    }
}
