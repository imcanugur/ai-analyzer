<?php

namespace App\Jobs;

use App\Enums\AnalysisStage;
use App\Models\Analysis;
use App\Traits\RunsAIStage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateSimilarityJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, RunsAIStage, SerializesModels;

    public function __construct(
        public readonly Analysis $analysis
    ) {}

    public function handle(): void
    {
        $extractResult = $this->analysis->results()
            ->where('stage', AnalysisStage::EXTRACT)
            ->first();

        $sourceText = $extractResult?->payload['text'] ?? '';

        $matches = '';

        $this->runStage(
            analysis: $this->analysis,
            stage: AnalysisStage::SIMILARITY,
            promptName: 'similarity',
            replacements: [
                'source' => $sourceText,
                'matches' => $matches,
            ]
        );
    }
}
