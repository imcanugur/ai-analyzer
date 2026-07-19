<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\StageRouteRepositoryInterface;
use App\Enums\AnalysisStatus;
use App\Jobs\ExecutePipelineStageJob;
use App\Jobs\ExtractTextJob;
use App\Jobs\GenerateReportJob;
use App\Models\Analysis;
use App\Models\AnalysisResult;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class PipelineService
{
    public function __construct(
        protected StageRouteRepositoryInterface $stageRouteRepository
    ) {}

    /**
     * Atomically resolve and execute the next pending pipeline stage for an analysis.
     * All pipeline steps (extract, AI stages, report generation) are 100% DB-driven.
     */
    public function runNextPendingStage(Analysis $analysis): void
    {
        $lockKey = "pipeline_lock:{$analysis->id}";
        $lock = Cache::lock($lockKey, 15);

        $lock->block(5, function () use ($analysis) {
            $analysis->refresh();

            // Stop execution if analysis is marked failed or completed
            if ($analysis->status === AnalysisStatus::FAILED || $analysis->status === AnalysisStatus::COMPLETED) {
                return;
            }

            $logPrefix = "[PipelineService][Analysis:{$analysis->id}]";
            $activeStages = $this->stageRouteRepository->getActiveOrdered();

            if ($activeStages->isEmpty()) {
                Log::warning("{$logPrefix} No active pipeline stages found in database.");
                return;
            }

            // Retrieve all existing result stage keys for this analysis
            $results = $analysis->results()->get();
            $handledStageKeys = $results->pluck('stage')->toArray();

            // Find the first active stage that has not been started yet
            $nextStageRoute = null;
            foreach ($activeStages as $stageRoute) {
                if (! in_array($stageRoute->stage, $handledStageKeys, true)) {
                    $nextStageRoute = $stageRoute;
                    break;
                }
            }

            // If a pending stage is found, dispatch it dynamically based on stage key
            if ($nextStageRoute) {
                Log::info("{$logPrefix} Starting dynamic stage '{$nextStageRoute->stage}' ({$nextStageRoute->name})");

                if ($nextStageRoute->stage === 'extract') {
                    ExtractTextJob::dispatch($analysis);
                    return;
                }

                if ($nextStageRoute->stage === 'report') {
                    // Mark report as processing
                    AnalysisResult::create([
                        'analysis_id' => $analysis->id,
                        'stage' => 'report',
                        'status' => AnalysisStatus::PROCESSING,
                        'payload' => [],
                        'metadata' => [
                            'stage_name' => $nextStageRoute->name ?? 'Final Report Generation',
                        ],
                    ]);

                    GenerateReportJob::dispatch($analysis);
                    return;
                }

                // Create PROCESSING result entry for AI stage to prevent race conditions
                AnalysisResult::create([
                    'analysis_id' => $analysis->id,
                    'node_id' => $nextStageRoute->node_id,
                    'model' => $nextStageRoute->model ?? 'gemma2',
                    'driver' => 'ollama',
                    'stage' => $nextStageRoute->stage,
                    'status' => AnalysisStatus::PROCESSING,
                    'payload' => [],
                    'metadata' => [
                        'stage_name' => $nextStageRoute->name ?? $nextStageRoute->stage,
                    ],
                ]);

                ExecutePipelineStageJob::dispatch($analysis, $nextStageRoute->stage);
                return;
            }

            // If no pending stages remain, check if any active stage is currently processing
            $hasProcessingStage = $results->contains(fn ($r) => $r->status === AnalysisStatus::PROCESSING || $r->status->value === 'processing');

            if (! $hasProcessingStage) {
                Log::info("{$logPrefix} All DB pipeline stages completed.");
            }
        });
    }
}
