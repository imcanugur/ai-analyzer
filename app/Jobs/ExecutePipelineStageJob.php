<?php

declare(strict_types=1);

namespace App\Jobs;

use App\AI\Contracts\AIProviderInterface;
use App\Contracts\NotificationServiceInterface;
use App\Contracts\StageRouteRepositoryInterface;
use App\Enums\AnalysisStatus;
use App\Models\Analysis;
use App\Models\AnalysisResult;
use App\Services\PipelineService;
use App\Services\PromptService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ExecutePipelineStageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 0;

    public function __construct(
        public readonly Analysis $analysis,
        public readonly string $stageKey
    ) {}

    public function tags(): array
    {
        return [
            'analysis',
            'stage:'.$this->stageKey,
            'analysis_id:'.$this->analysis->id,
        ];
    }

    public function handle(
        StageRouteRepositoryInterface $routeRepository,
        PromptService $promptService,
        AIProviderInterface $aiProvider,
        PipelineService $pipelineService
    ): void {
        $logPrefix = "[PipelineJob:{$this->stageKey}][Analysis:{$this->analysis->id}]";
        $stageRoute = $routeRepository->findByStage($this->stageKey);

        $resultRecord = AnalysisResult::where('analysis_id', $this->analysis->id)
            ->where('stage', $this->stageKey)
            ->first();

        if (! $stageRoute || ! $stageRoute->is_active) {
            Log::warning("{$logPrefix} Stage is inactive or missing. Skipping to next stage.");
            if ($resultRecord) {
                $resultRecord->delete();
            }
            $pipelineService->runNextPendingStage($this->analysis);

            return;
        }

        $model = $stageRoute->model ?? 'gemma2';

        try {
            // 1. Retrieve extracted text
            $extractResult = $this->analysis->results()
                ->where('stage', 'extract')
                ->first();

            $text = $extractResult?->payload['text'] ?? '';

            if (empty($text) || str_starts_with($text, '[Stubbed Extracted Content')) {
                throw new \RuntimeException("Valid extracted text is missing for pipeline stage: {$this->stageKey}.");
            }

            // 2. Fetch outputs from previous completed stages
            $previousResults = [];
            $allCompletedResults = $this->analysis->results()
                ->where('status', AnalysisStatus::COMPLETED)
                ->get();

            foreach ($allCompletedResults as $res) {
                if (is_array($res->payload) && isset($res->payload['text'])) {
                    $previousResults[$res->stage] = $res->payload['text'];
                }
            }

            // 3. Resolve system prompt
            $systemPrompt = ! empty($stageRoute->system_prompt)
                ? $stageRoute->system_prompt
                : $promptService->get('system');

            // 4. Resolve user prompt & interpolate placeholders
            if (! empty($stageRoute->prompt_template)) {
                $userPrompt = str_replace(['{{ text }}', '{{text}}'], $text, $stageRoute->prompt_template);
                foreach ($previousResults as $pStage => $pContent) {
                    $userPrompt = str_replace(["{{ {$pStage}_output }}", "{{{$pStage}_output}}"], $pContent, $userPrompt);
                }
            } else {
                $userPrompt = $promptService->render($this->stageKey, array_merge(['text' => $text], $previousResults));
            }

            Log::info("{$logPrefix} Initiating AI provider execution (Model: {$model})");

            // 5. Build AI parameters
            $options = [
                'stage' => $this->stageKey,
                'model' => $model,
            ];
            if ($stageRoute->temperature !== null) {
                $options['temperature'] = $stageRoute->temperature;
            }
            if ($stageRoute->max_tokens !== null) {
                $options['max_tokens'] = $stageRoute->max_tokens;
            }

            // 6. Execute AI generation
            $aiResponse = $aiProvider->generate($userPrompt, $options, $systemPrompt);

            // 7. Update result record to COMPLETED
            if (! $resultRecord) {
                $resultRecord = new AnalysisResult([
                    'analysis_id' => $this->analysis->id,
                    'stage' => $this->stageKey,
                ]);
            }

            $resultRecord->fill([
                'node_id' => $aiResponse->metadata['node_id'] ?? $stageRoute->node_id,
                'model' => $model,
                'driver' => $aiResponse->metadata['driver'] ?? 'ollama',
                'status' => AnalysisStatus::COMPLETED,
                'payload' => [
                    'text' => $aiResponse->text,
                ],
                'metadata' => [
                    'stage_name' => $stageRoute->name ?? $this->stageKey,
                ],
                'execution_time' => $aiResponse->executionTime,
                'tokens' => $aiResponse->tokens,
            ]);
            $resultRecord->save();

            Log::info("{$logPrefix} Stage executed successfully.");

            // 8. Trigger next stage
            $pipelineService->runNextPendingStage($this->analysis);

        } catch (\Exception $e) {
            Log::error("{$logPrefix} Stage failed: ".$e->getMessage());

            if ($resultRecord) {
                $resultRecord->update([
                    'status' => AnalysisStatus::FAILED,
                    'payload' => ['error' => $e->getMessage()],
                ]);
            }

            if ($stageRoute->on_failure === 'fail_pipeline') {
                $this->analysis->update([
                    'status' => AnalysisStatus::FAILED,
                    'error' => "[{$this->stageKey}] Critical stage failed: ".$e->getMessage(),
                    'completed_at' => now(),
                ]);

                $user = $this->analysis->submission?->user;
                if ($user) {
                    app(NotificationServiceInterface::class)->send(
                        $user,
                        'Analysis Failed',
                        "Analysis failed during stage [{$stageRoute->name}].",
                        'heroicon-o-x-circle',
                        'danger'
                    );
                }
            } else {
                Log::warning("{$logPrefix} Failure skipped per stage policy. Moving to next stage...");
                $pipelineService->runNextPendingStage($this->analysis);
            }
        }
    }
}
