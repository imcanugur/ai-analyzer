<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\StageRouteRepositoryInterface;
use App\Enums\AnalysisStatus;
use App\Jobs\ExecutePipelineStageJob;
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
                Log::warning("{$logPrefix} No active pipeline stages found. Generating report...");
                GenerateReportJob::dispatch($analysis);
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

            // If a pending stage is found, mark it as PROCESSING and dispatch job
            if ($nextStageRoute) {
                Log::info("{$logPrefix} Starting stage '{$nextStageRoute->stage}'");

                // Atomically create PROCESSING result entry to prevent race condition re-dispatches
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

            // If no pending stages remain, verify if any active stage is currently processing
            $hasProcessingStage = $results->contains(fn ($r) => $r->status === AnalysisStatus::PROCESSING || $r->status->value === 'processing');

            if (! $hasProcessingStage) {
                Log::info("{$logPrefix} All pipeline stages finished. Dispatching report generation.");
                
                GenerateReportJob::dispatch($analysis);
            }
        });
    }
}
