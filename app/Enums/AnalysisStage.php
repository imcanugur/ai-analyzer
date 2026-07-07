<?php

namespace App\Enums;

enum AnalysisStage: string
{
    case EXTRACT = 'extract';
    case SUMMARY = 'summary';
    case GRAMMAR = 'grammar';
    case REFERENCES = 'references';
    case SIMILARITY = 'similarity';
    case REVIEWER = 'reviewer';
    case PLAGIARISM = 'plagiarism';
    case READABILITY = 'readability';
    case REPORT = 'report';
}
