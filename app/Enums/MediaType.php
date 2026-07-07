<?php

namespace App\Enums;

enum MediaType: string
{
    case DOCUMENT = 'document';
    case IMAGE = 'image';
    case AUDIO = 'audio';
    case VIDEO = 'video';
    case SOURCE_CODE = 'source-code';
}
