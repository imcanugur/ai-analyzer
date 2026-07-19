<?php

declare(strict_types=1);

namespace App\Services\Extractors;

class TextNormalizer
{
    /**
     * Clean and sanitize raw extracted text for optimal AI LLM ingestion.
     * Automatically strips zero-width spaces (\u200b), soft hyphens, control characters,
     * and unwraps broken column line breaks into clean paragraphs.
     */
    public function normalize(string $text): string
    {
        if (empty($text)) {
            return '';
        }

        // 1. Standardize EOL (\r\n and \r -> \n)
        $text = str_replace(["\r\n", "\r"], "\n", $text);

        // 2. Remove Zero-Width Spaces & Invisible Control Bytes (both UTF-8 bytes and literal \u200b string sequences)
        $invisibleChars = [
            "\u{200B}", "\u{200C}", "\u{200D}", "\u{FEFF}", "\u{00AD}",
            "\xE2\x80\x8B", "\xE2\x80\x8C", "\xE2\x80\x8D", "\xEF\xBB\xBF", "\xC2\xAD",
        ];
        $text = str_replace($invisibleChars, ' ', $text);

        // Strip literal 6-character string sequences like \u200b, \u200B, \u00ad, \ufeff
        $text = preg_replace('/\\\\u(?:200[bBcCdD]|00[aA][dD]|[fF][eE][fF][fF])/u', ' ', $text) ?? $text;

        // 3. Remove null bytes and ASCII control characters (except \n and \t)
        $text = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $text) ?? $text;

        // 4. Fix broken line-wrap hyphenation (e.g. "acade-\nmic" -> "academic")
        $text = preg_replace('/(\b[a-zA-ZçğıöşüÇĞİÖŞÜ]+)-\s*\n\s*([a-zA-ZçğıöşüÇĞİÖŞÜ]+\b)/u', '$1$2', $text) ?? $text;

        // 5. Unwrap single line breaks within paragraphs
        $lines = explode("\n", $text);
        $unwrapped = [];
        $currentParagraph = '';

        foreach ($lines as $line) {
            $trimmedLine = trim($line);

            if (empty($trimmedLine)) {
                if (! empty($currentParagraph)) {
                    $unwrapped[] = $currentParagraph;
                    $currentParagraph = '';
                }
                continue;
            }

            if (empty($currentParagraph)) {
                $currentParagraph = $trimmedLine;
            } else {
                $lastChar = mb_substr($currentParagraph, -1);
                // If previous line ends with sentence punctuation or current line starts with section header, start new line
                if (in_array($lastChar, ['.', ':', ';', '!', '?'], true) || preg_match('/^(?:[I|V|X]+\.|\d+\.|\bullet|•|[A-Z]\.)/u', $trimmedLine)) {
                    $unwrapped[] = $currentParagraph;
                    $currentParagraph = $trimmedLine;
                } else {
                    $currentParagraph .= ' '.$trimmedLine;
                }
            }
        }

        if (! empty($currentParagraph)) {
            $unwrapped[] = $currentParagraph;
        }

        $text = implode("\n\n", $unwrapped);

        // 6. Collapse horizontal whitespace (spaces & tabs) to a single space
        $text = preg_replace('/[ \t]+/u', ' ', $text) ?? $text;

        // 7. Collapse excessive consecutive newlines down to 2 newlines
        $text = preg_replace('/\n{3,}/u', "\n\n", $text) ?? $text;

        return trim($text);
    }
}
