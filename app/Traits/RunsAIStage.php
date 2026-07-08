<?php

namespace App\Traits;

use App\AI\Contracts\AIProviderInterface;
use App\Contracts\AnalysisResultRepositoryInterface;
use App\Enums\AnalysisStage;
use App\Enums\AnalysisStatus;
use App\Models\Analysis;
use App\Services\PromptService;

trait RunsAIStage
{
    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 0;

    /**
     * Run an AI stage: load prompt, call provider, save result, dispatch next.
     */
    protected function runStage(
        Analysis $analysis,
        AnalysisStage $stage,
        string $promptName,
        ?string $nextJobClass = null
    ): void {
        $resultRepository = app(AnalysisResultRepositoryInterface::class);
        $promptService = app(PromptService::class);
        $aiProvider = app(AIProviderInterface::class);

        try {
            // 1. Get extracted text from the extract stage result
            $extractResult = $analysis->results()
                ->where('stage', AnalysisStage::EXTRACT)
                ->first();

            $text = $extractResult?->payload['text'] ?? '';

            if (empty($text)) {
                throw new \RuntimeException("No extracted text found for analysis: {$analysis->id}");
            }

            // 2. Load prompt template and fill placeholders
            $prompt = $promptService->load($promptName, ['text' => $text]);

            // 3. Call AI provider
            $aiResponse = $aiProvider->generate($prompt);

            // 4. Save the stage result
            $resultRepository->create([
                'analysis_id' => $analysis->id,
                'stage' => $stage,
                'status' => AnalysisStatus::COMPLETED,
                'payload' => [
                    'text' => $aiResponse->text,
                ],
                'metadata' => [
                    'model' => config('ai.providers.ollama.default_model'),
                    'provider' => config('ai.default'),
                ],
                'execution_time' => $aiResponse->executionTime,
                'tokens' => $aiResponse->tokens,
            ]);

            // 5. Dispatch next job or mark analysis as completed
            if ($nextJobClass) {
                $nextJobClass::dispatch($analysis);
            } else {
                $analysis->update([
                    'status' => AnalysisStatus::COMPLETED,
                    'completed_at' => now(),
                ]);
            }

        } catch (\Exception $e) {
            $analysis->update([
                'status' => AnalysisStatus::FAILED,
                'error' => "[{$stage->value}] ".$e->getMessage(),
                'completed_at' => now(),
            ]);
        }
    }
}
