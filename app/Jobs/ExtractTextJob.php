<?php

namespace App\Jobs;

use App\Contracts\AnalysisResultRepositoryInterface;
use App\Enums\AnalysisStage;
use App\Enums\AnalysisStatus;
use App\Models\Analysis;
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
     * Execute the job.
     */
    public function handle(AnalysisResultRepositoryInterface $resultRepository): void
    {
        $submission = $this->analysis->submission;
        $media = $submission?->media()->first();
        $extractedText = '';

        if ($media) {
            try {
                $disk = Storage::disk($media->disk);
                if ($disk->exists($media->path)) {
                    $mime = $media->mime ?? '';
                    $extension = strtolower($media->extension);

                    // If it is a text-based format, read its content directly
                    if (str_starts_with($mime, 'text/') || in_array($extension, ['json', 'xml', 'sql', 'css', 'js', 'py', 'php'])) {
                        $extractedText = $disk->get($media->path);
                    } elseif ($extension === 'pdf') {
                        if (class_exists(\Smalot\PdfParser\Parser::class)) {
                            $parser = new \Smalot\PdfParser\Parser();
                            $pdf = $parser->parseContent($disk->get($media->path));
                            $extractedText = $pdf->getText();
                        } else {
                            $extractedText = "[Stubbed Extracted Content for binary file: {$media->original_name} - smalot/pdfparser library is missing. Run 'composer require smalot/pdfparser' to enable PDF parsing.]";
                        }
                    } else {
                        // Stub for binary formats (to be integrated with AI extraction in Sprint 9)
                        $extractedText = "[Stubbed Extracted Content for binary file: {$media->original_name}]";
                    }
                }
            } catch (\Exception $e) {
                // If text extraction fails, record the error
                $this->analysis->update([
                    'status' => AnalysisStatus::FAILED,
                    'error' => $e->getMessage(),
                    'completed_at' => now(),
                ]);

                return;
            }
        }

        // 3. Save the result of the extraction stage using the repository
        $resultRepository->create([
            'analysis_id' => $this->analysis->id,
            'stage' => AnalysisStage::EXTRACT,
            'status' => AnalysisStatus::COMPLETED,
            'payload' => [
                'text' => $extractedText,
            ],
            'metadata' => [
                'extractor' => 'default_parser',
                'mime_type' => $media?->mime,
                'file_size' => $media?->size,
            ],
            'execution_time' => 0,
        ]);

        // 4. Dispatch the first AI stage job
        GenerateSummaryJob::dispatch($this->analysis);
    }
}
