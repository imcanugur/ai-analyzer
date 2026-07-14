<?php

declare(strict_types=1);

namespace App\Traits;

use App\AI\Contracts\AIProviderInterface;
use App\Contracts\AnalysisResultRepositoryInterface;
use App\Contracts\NotificationServiceInterface;
use App\Contracts\StageRouteRepositoryInterface;
use App\Enums\AnalysisStage;
use App\Enums\AnalysisStatus;
use App\Models\Analysis;
use App\Services\PromptService;
use Illuminate\Support\Facades\Log;

trait RunsAIStage
{
    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 0;

    /**
     * Get the tags that should be assigned to the job.
     *
     * @return array<int, string>
     */
    public function tags(): array
    {
        return [
            'analysis',
            'pipeline_stage',
            'analysis_id:' . $this->analysis->id,
            'submission_id:' . ($this->analysis->submission_id ?? 'none'),
        ];
    }

    /**
     * Run an AI stage: load system + user prompts, call provider, save result, dispatch next.
     *
     * @param  array  $replacements  Custom placeholder replacements (default: ['text' => extracted_text])
     */
    protected function runStage(
        Analysis $analysis,
        AnalysisStage $stage,
        string $promptName,
        array $replacements = []
    ): void {
        $resultRepository = app(AnalysisResultRepositoryInterface::class);
        $promptService = app(PromptService::class);
        $aiProvider = app(AIProviderInterface::class);
        $routeRepository = app(StageRouteRepositoryInterface::class);

        $stageRoute = $routeRepository->findByStage($stage->value);
        $model = $stageRoute ? $stageRoute->model : 'gemma2';

        // Resolve next job dynamically from central pipeline configuration
        $pipeline = config('ai.pipeline', []);
        $nextJobClass = $pipeline[$stage->value] ?? null;

        $logPrefix = "[AI-Stage:{$stage->value}][Analysis:{$analysis->id}]";

        Log::info("{$logPrefix} Stage başlatılıyor.", [
            'stage' => $stage->value,
            'analysis_id' => $analysis->id,
            'prompt_name' => $promptName,
            'next_job' => $nextJobClass,
            'custom_replacements' => ! empty($replacements),
            'model' => $model,
        ]);

        try {
            // 1. Get extracted text from the extract stage result
            $extractResult = $analysis->results()
                ->where('stage', AnalysisStage::EXTRACT)
                ->first();

            $text = $extractResult?->payload['text'] ?? '';

            // Check if text is empty or a stub/placeholder
            if (empty($text)) {
                Log::error("{$logPrefix} Extract edilmiş metin bulunamadı.", [
                    'extract_result_exists' => $extractResult !== null,
                    'payload_keys' => $extractResult ? array_keys($extractResult->payload ?? []) : [],
                ]);
                throw new \RuntimeException("No extracted text found for analysis: {$analysis->id}");
            }

            if (str_starts_with($text, '[Stubbed Extracted Content')) {
                Log::error("{$logPrefix} PDF/Binary dosya metin çıkarımı henüz yapılmamış, taslak (stub) veri tespit edildi. AI analizi iptal ediliyor.", [
                    'text' => $text,
                ]);
                throw new \RuntimeException('Text extraction is not yet implemented or failed for this file type (Stub detected). Please make sure PDF text extraction is active.');
            }

            Log::info("{$logPrefix} Metin alındı.", [
                'text_length' => mb_strlen($text),
                'text_preview' => mb_substr($text, 0, 200).'...',
            ]);

            // 2. Load system prompt (shared across all stages)
            $systemPrompt = $promptService->get('system');

            Log::info("{$logPrefix} System prompt yüklendi.", [
                'system_prompt_length' => mb_strlen($systemPrompt),
                'system_prompt' => $systemPrompt,
            ]);

            // 3. Build replacements — use custom if provided, otherwise default to text
            if (empty($replacements)) {
                $replacements = ['text' => $text];
            }

            Log::info("{$logPrefix} Replacement'lar hazırlandı.", [
                'replacement_keys' => array_keys($replacements),
                'replacement_lengths' => array_map(fn ($v) => mb_strlen((string) $v), $replacements),
                'replacements' => $replacements,
                'full_text' => $text,
            ]);

            // 4. Render the user prompt template with placeholders
            $userPrompt = $promptService->render($promptName, $replacements);

            Log::info("{$logPrefix} User prompt render edildi.", [
                'prompt_name' => $promptName,
                'user_prompt_length' => mb_strlen($userPrompt),
                'user_prompt_preview' => mb_substr($userPrompt, 0, 500).'...',
                'full_user_prompt' => $userPrompt,
                'full_text' => $text,
            ]);

            // 5. Call AI provider with system + user prompt separation
            Log::info("{$logPrefix} AI provider'a istek gönderiliyor...", [
                'model' => $model,
                'system_prompt_length' => mb_strlen($systemPrompt),
                'user_prompt_length' => mb_strlen($userPrompt),
                'total_input_length' => mb_strlen($systemPrompt) + mb_strlen($userPrompt),
                'full_system_prompt' => $systemPrompt,
                'full_user_prompt' => $userPrompt,
            ]);

            $aiResponse = $aiProvider->generate($userPrompt, [
                'stage' => $stage->value,
                'model' => $model,
            ], $systemPrompt);

            Log::info("{$logPrefix} AI yanıtı alındı.", [
                'response_length' => mb_strlen($aiResponse->text),
                'tokens' => $aiResponse->tokens,
                'execution_time_ms' => $aiResponse->executionTime,
                'response_preview' => mb_substr($aiResponse->text, 0, 500).'...',
            ]);

            // 6. JSON parse kontrolü (debug amaçlı)
            $jsonDecoded = json_decode($aiResponse->text, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::warning("{$logPrefix} AI yanıtı geçerli JSON değil!", [
                    'json_error' => json_last_error_msg(),
                    'raw_response_start' => mb_substr($aiResponse->text, 0, 300),
                    'raw_response_end' => mb_substr($aiResponse->text, -300),
                ]);
            } else {
                Log::info("{$logPrefix} AI yanıtı geçerli JSON.", [
                    'json_keys' => array_keys($jsonDecoded),
                ]);
            }

            // 7. Save the stage result
            $resultRepository->create([
                'analysis_id' => $analysis->id,
                'node_id' => $aiResponse->metadata['node_id'] ?? null,
                'model' => $model,
                'driver' => $aiResponse->metadata['driver'] ?? 'ollama',
                'stage' => $stage,
                'status' => AnalysisStatus::COMPLETED,
                'payload' => [
                    'text' => $aiResponse->text,
                ],
                'metadata' => null,
                'execution_time' => $aiResponse->executionTime,
                'tokens' => $aiResponse->tokens,
            ]);

            Log::info("{$logPrefix} Sonuç kaydedildi.", [
                'status' => 'completed',
            ]);

            // 8. Dispatch next job or mark analysis as completed
            if ($nextJobClass) {
                Log::info("{$logPrefix} Sonraki job dispatch ediliyor.", [
                    'next_job' => $nextJobClass,
                ]);
                $nextJobClass::dispatch($analysis);
            } else {
                Log::info("{$logPrefix} Son stage tamamlandı. Analiz completed olarak işaretleniyor.");
                $analysis->update([
                    'status' => AnalysisStatus::COMPLETED,
                    'completed_at' => now(),
                ]);

                // Send database notification to the user
                $user = $analysis->submission?->user;
                if ($user) {
                    app(NotificationServiceInterface::class)->send(
                        $user,
                        'Analysis Completed!',
                        "The manuscript analysis for '{$analysis->submission->title}' has finished successfully.",
                        'heroicon-o-check-circle',
                        'success'
                    );
                }
            }

            Log::info("{$logPrefix} Stage başarıyla tamamlandı.", [
                'total_execution_time_ms' => $aiResponse->executionTime,
                'total_tokens' => $aiResponse->tokens,
            ]);

        } catch (\Exception $e) {
            Log::error("{$logPrefix} Stage BAŞARISIZ!", [
                'error_message' => $e->getMessage(),
                'error_class' => get_class($e),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'trace_summary' => collect(array_slice($e->getTrace(), 0, 5))
                    ->map(fn ($t) => ($t['class'] ?? '').'::'.($t['function'] ?? '').':'.($t['line'] ?? ''))
                    ->toArray(),
            ]);

            $analysis->update([
                'status' => AnalysisStatus::FAILED,
                'error' => "[{$stage->value}] ".$e->getMessage(),
                'completed_at' => now(),
            ]);

            // Send database notification to the user
            $user = $analysis->submission?->user;
            if ($user) {
                app(NotificationServiceInterface::class)->send(
                    $user,
                    'Analysis Failed',
                    "The manuscript analysis for '{$analysis->submission->title}' failed during [{$stage->value}] stage.",
                    'heroicon-o-x-circle',
                    'danger'
                );
            }
        }
    }
}
