<?php

namespace App\Support;

use App\Contracts\MediaTypeResolver;
use App\Enums\MediaType;
use Illuminate\Support\Str;
use InvalidArgumentException;

class DefaultMediaTypeResolver implements MediaTypeResolver
{
    /**
     * Resolve the MediaType enum based on mime type and extension.
     */
    public function resolve(string $mime, string $extension): MediaType
    {
        $extension = strtolower($extension);

        $restrictedExtensions = ['exe', 'dll', 'msi', 'dmg', 'iso', 'so', 'bin', 'jar'];
        if (in_array($extension, $restrictedExtensions)) {
            throw new InvalidArgumentException("Security Alert: Executable files and binary installers are not allowed: .{$extension}");
        }

        $sourceCodeExtensions = [
            'php', 'py', 'js', 'jsx', 'ts', 'tsx', 'go', 'java', 'c', 'cpp',
            'h', 'cs', 'rb', 'rs', 'swift', 'sh', 'bat', 'html', 'css', 'sql',
            'json', 'yaml', 'yml', 'xml', 'md',
        ];
        if (in_array($extension, $sourceCodeExtensions)) {
            return MediaType::SOURCE_CODE;
        }

        if (Str::startsWith($mime, 'image/')) {
            return MediaType::IMAGE;
        }

        if (Str::startsWith($mime, 'audio/')) {
            return MediaType::AUDIO;
        }

        if (Str::startsWith($mime, 'video/')) {
            return MediaType::VIDEO;
        }

        $documentMimes = [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.ms-powerpoint',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'text/plain',
            'text/csv',
            'application/rtf',
        ];
        if (in_array($mime, $documentMimes) || in_array($extension, ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'csv', 'rtf'])) {
            return MediaType::DOCUMENT;
        }

        return MediaType::DOCUMENT;
    }
}
