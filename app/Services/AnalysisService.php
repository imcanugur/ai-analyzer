<?php

namespace App\Services;

use App\Contracts\AnalysisRepositoryInterface;
use App\Models\Analysis;
use App\Models\Submission;
use App\Enums\AnalysisStatus;

class AnalysisService
{
    public function __construct(
        protected AnalysisRepositoryInterface $analysisRepository
    ) {}

    /**
     * Create a new analysis record for a submission with Pending status.
     *
     * @param Submission $submission
     * @param string $type
     * @param array $options
     * @return Analysis
     */
    public function createAnalysis(Submission $submission, string $type, array $options = []): Analysis
    {
        return $this->analysisRepository->create([
            'submission_id' => $submission->id,
            'type' => $type,
            'status' => AnalysisStatus::PENDING,
            'category' => $options['category'] ?? null,
            'provider' => $options['provider'] ?? null,
            'engine' => $options['engine'] ?? null,
            'model' => $options['model'] ?? null,
            'version' => $options['version'] ?? null,
            'config' => $options['config'] ?? null,
            'metadata' => $options['metadata'] ?? null,
        ]);
    }
}
