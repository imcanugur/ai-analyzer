<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Log;

class JsonRepairService
{
    /**
     * Parse and auto-repair malformed JSON string returned by LLM models.
     */
    public function repairAndDecode(string $jsonString): array
    {
        $clean = trim($jsonString);

        // 1. Strip Markdown code fences if present (e.g. ```json ... ```)
        if (str_contains($clean, '```')) {
            $clean = preg_replace('/^```(?:json)?\s*/i', '', $clean) ?? $clean;
            $clean = preg_replace('/\s*```$/', '', $clean) ?? $clean;
            $clean = trim($clean);
        }

        // 2. Extract first '{' to last '}' block if extra text surrounds JSON
        $firstBrace = strpos($clean, '{');
        $lastBrace = strrpos($clean, '}');

        if ($firstBrace !== false && $lastBrace !== false && $lastBrace > $firstBrace) {
            $clean = substr($clean, $firstBrace, $lastBrace - $firstBrace + 1);
        }

        // 3. Try standard JSON decode first
        $decoded = json_decode($clean, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return $decoded;
        }

        Log::warning('[JsonRepairService] Primary JSON decode failed. Attempting structural repair.', [
            'error' => json_last_error_msg(),
        ]);

        // 4. Structural Repair: Fix unescaped newlines in JSON strings
        $repaired = preg_replace_callback('/"([^"\\\\]*(\\.[^"\\\\]*)*)"/s', function ($matches) {
            return '"'.str_replace(["\n", "\r", "\t"], ["\\n", "\\r", "\\t"], $matches[1]).'"';
        }, $clean) ?? $clean;

        // 5. Fix trailing commas before closing braces or brackets (e.g. `,"}` -> `"}`)
        $repaired = preg_replace('/,\s*([\}\]])/', '$1', $repaired) ?? $repaired;

        // 6. Try decode again after structural repair
        $decoded = json_decode($repaired, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            Log::info('[JsonRepairService] Structural JSON repair succeeded.');
            return $decoded;
        }

        // 7. Auto-close truncated JSON (e.g. missing trailing braces/quotes)
        $repaired = $this->autoCloseTruncatedJson($repaired);
        $decoded = json_decode($repaired, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            Log::info('[JsonRepairService] Truncated JSON auto-close repair succeeded.');
            return $decoded;
        }

        Log::error('[JsonRepairService] JSON repair unresolvable.', ['raw' => mb_substr($jsonString, 0, 300)]);

        return ['raw_text' => $jsonString, 'parse_error' => true];
    }

    /**
     * Auto-close missing brackets and quotes for truncated LLM responses.
     */
    protected function autoCloseTruncatedJson(string $json): string
    {
        $openBraces = substr_count($json, '{') - substr_count($json, '}');
        $openBrackets = substr_count($json, '[') - substr_count($json, ']');

        // Close unclosed quote
        if (substr_count($json, '"') % 2 !== 0) {
            $json .= '"';
        }

        // Append missing closing brackets
        while ($openBrackets > 0) {
            $json .= ']';
            $openBrackets--;
        }

        // Append missing closing braces
        while ($openBraces > 0) {
            $json .= '}';
            $openBraces--;
        }

        return $json;
    }
}
