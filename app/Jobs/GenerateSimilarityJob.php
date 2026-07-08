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
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, RunsAIStage;

    public function __construct(
        public readonly Analysis $analysis
    ) {}

    public function handle(): void
    {
        $this->runStage(
            analysis: $this->analysis,
            stage: AnalysisStage::SIMILARITY,
            promptName: 'similarity',
            nextJobClass: GenerateReviewerJob::class
        );
    }
}
