<?php

namespace App\Contracts;

use App\Enums\MediaType;

interface MediaTypeResolver
{
    /**
     * Resolve the MediaType enum based on mime type and extension.
     */
    public function resolve(string $mime, string $extension): MediaType;
}
