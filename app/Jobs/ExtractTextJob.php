<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Contracts\AnalysisResultRepositoryInterface;
use App\Enums\AnalysisStatus;
use App\Models\Analysis;
use App\Services\Extractors\ExtractorManager;
use App\Services\PipelineService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class ExtractTextJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public readonly Analysis $analysis
    ) {}

    /**
     * Get the tags that should be assigned to the job.
     *
     * @return array<int, string>
     */
    public function tags(): array
    {
        return [
            'analysis',
            'extract',
            'analysis_id:'.$this->analysis->id,
            'submission_id:'.($this->analysis->submission_id ?? 'none'),
        ];
    }

    /**
     * Execute the text extraction job using Strategy Pattern.
     */
    public function handle(
        AnalysisResultRepositoryInterface $resultRepository,
        ExtractorManager $extractorManager,
        PipelineService $pipelineService
    ): void {
        $submission = $this->analysis->submission;
        $media = $submission?->media()->first();
        $extractedText = '';

        if ($media) {
            try {
                $disk = Storage::disk($media->disk);
                if ($disk->exists($media->path)) {
                    $fileContents = $disk->get($media->path);
                    $mime = $media->mime ?? 'application/octet-stream';
                    $extension = strtolower($media->extension ?? '');

                    // Extract & normalize text using ExtractorManager strategy
                    $extractedText = $extractorManager->extractAndNormalize($fileContents, $mime, $extension);
                }
            } catch (Exception $e) {
                $this->analysis->update([
                    'status' => AnalysisStatus::FAILED,
                    'error' => '[extract] '.$e->getMessage(),
                    'completed_at' => now(),
                ]);

                return;
            }
        }

        // Save extraction result into database
        $resultRepository->create([
            'analysis_id' => $this->analysis->id,
            'stage' => 'extract',
            'status' => AnalysisStatus::COMPLETED,
            'payload' => [
                'text' => $extractedText,
            ],
            'metadata' => [
                'extractor' => 'ExtractorManager',
                'mime_type' => $media?->mime,
                'file_size' => $media?->size,
                'text_length' => mb_strlen($extractedText),
            ],
            'execution_time' => 0,
        ]);

        // Continue dynamic DB pipeline
        $pipelineService->runNextPendingStage($this->analysis);
    }
}
