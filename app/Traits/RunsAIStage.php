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
     * Run an AI stage: load system + user prompts, call provider, save result, dispatch next.
     *
     * @param  array  $replacements  Custom placeholder replacements (default: ['text' => extracted_text])
     */
    protected function runStage(
        Analysis $analysis,
        AnalysisStage $stage,
        string $promptName,
        ?string $nextJobClass = null,
        array $replacements = []
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

            // 2. Load system prompt (shared across all stages)
            $systemPrompt = $promptService->get('system');

            // 3. Build replacements — use custom if provided, otherwise default to text
            if (empty($replacements)) {
                $replacements = ['text' => $text];
            }

            // 4. Render the user prompt template with placeholders
            $userPrompt = $promptService->render($promptName, $replacements);

            // 5. Call AI provider with system + user prompt separation
            $aiResponse = $aiProvider->generate($userPrompt, [], $systemPrompt);


            logger()->imfpr

            // 6. Save the stage result
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

            // 7. Dispatch next job or mark analysis as completed
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
