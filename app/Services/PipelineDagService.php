<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\NotificationServiceInterface;
use App\Contracts\StageRouteRepositoryInterface;
use App\Enums\AnalysisStatus;
use App\Jobs\ExecutePipelineStageJob;
use App\Jobs\GenerateReportJob;
use App\Models\Analysis;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class PipelineDagService
{
    public function __construct(
        protected StageRouteRepositoryInterface $stageRouteRepository,
        protected NotificationServiceInterface $notificationService
    ) {}

    /**
     * Start or evaluate the DAG pipeline for an analysis.
     * Evaluates ready stages and dispatches them in parallel.
     */
    public function evaluateAndDispatch(Analysis $analysis): void
    {
        $lock = Cache::lock("pipeline_dag_eval:{$analysis->id}", 10);

        $callback = function () use ($analysis) {
            $logPrefix = "[DAG-Engine][Analysis:{$analysis->id}]";

            // If analysis failed or already completed, do nothing
            if ($analysis->status === AnalysisStatus::FAILED || $analysis->status === AnalysisStatus::COMPLETED) {
                return;
            }

            $activeStages = $this->stageRouteRepository->getActiveOrdered();

            if ($activeStages->isEmpty()) {
                Log::warning("{$logPrefix} No active pipeline stages found in database.");
                GenerateReportJob::dispatch($analysis);

                return;
            }

            // Fetch existing stage execution statuses from database
            $results = $analysis->results()->get();
            $stageStatuses = [];
            foreach ($results as $res) {
                $stageStatuses[$res->stage] = $res->status->value ?? (string) $res->status;
            }

            // Determine which stages have already been executed or queued
            $executedStageKeys = array_keys($stageStatuses);

            // Find stages that are ready to run
            $readyStages = [];
            $allStagesCompleted = true;
            $hasUnrecoverableFailure = false;

            foreach ($activeStages as $stageRoute) {
                $status = $stageStatuses[$stageRoute->stage] ?? null;

                if ($status === 'completed' || $status === 'failed') {
                    if ($status === 'failed' && $stageRoute->on_failure === 'fail_pipeline') {
                        $hasUnrecoverableFailure = true;
                    }

                    continue;
                }

                $allStagesCompleted = false;

                // Check if stage's prerequisite dependencies are satisfied
                if (! in_array($stageRoute->stage, $executedStageKeys, true)) {
                    if ($stageRoute->isReadyToExecute($stageStatuses)) {
                        $readyStages[] = $stageRoute;
                    }
                }
            }

            if ($hasUnrecoverableFailure) {
                Log::error("{$logPrefix} Pipeline halted due to unrecoverable stage failure.");
                $analysis->update([
                    'status' => AnalysisStatus::FAILED,
                    'completed_at' => now(),
                ]);

                return;
            }

            // If any new stages are ready, dispatch them in parallel!
            if (! empty($readyStages)) {
                foreach ($readyStages as $stageToDispatch) {
                    Log::info("{$logPrefix} Dispatching ready stage '{$stageToDispatch->stage}' (Parallel DAG step)");
                    ExecutePipelineStageJob::dispatch($analysis, $stageToDispatch->stage);
                }

                return;
            }

            // If no ready stages remain and all active stages finished, trigger final report generation
            if ($allStagesCompleted) {
                Log::info("{$logPrefix} All DAG pipeline stages completed successfully. Dispatching report generation.");
                GenerateReportJob::dispatch($analysis);
            }
        };

        if (config('queue.default') === 'sync' || config('demo.enabled', false)) {
            $callback();
        } else {
            $lock->block(5, $callback);
        }
    }

    /**
     * Called by ExecutePipelineStageJob when a stage finishes (success or handled failure).
     */
    public function onStageCompleted(Analysis $analysis, string $stageKey): void
    {
        Log::info("[DAG-Engine][Analysis:{$analysis->id}] Stage '{$stageKey}' finished execution. Re-evaluating DAG graph...");
        $this->evaluateAndDispatch($analysis);
    }
}
