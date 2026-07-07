<?php

namespace App\Actions;

use App\DTO\StartAnalysisDTO;
use App\Models\Analysis;
use App\Services\AnalysisService;

class StartAnalysisAction
{
    public function __construct(
        protected AnalysisService $analysisService
    ) {}

    /**
     * Start an analysis by creating the analysis record with Pending status.
     *
     * @param StartAnalysisDTO $dto
     * @return Analysis
     */
    public function execute(StartAnalysisDTO $dto): Analysis
    {
        $analysis = $this->analysisService->createAnalysis(
            submission: $dto->submission,
            type: $dto->type,
            options: [
                'category' => $dto->category,
                'provider' => $dto->provider,
                'engine' => $dto->engine,
                'model' => $dto->model,
                'version' => $dto->version,
                'config' => $dto->config,
                'metadata' => $dto->metadata,
            ]
        );

        // Dispatch the queue pipeline to start analysis asynchronously
        \App\Jobs\StartAnalysisJob::dispatch($analysis);

        return $analysis;
    }
}
