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

class GenerateGrammarJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, RunsAIStage, SerializesModels;

    public function __construct(
        public readonly Analysis $analysis
    ) {}

    public function handle(): void
    {
        $this->runStage(
            analysis: $this->analysis,
            stage: AnalysisStage::GRAMMAR,
            promptName: 'grammar',
            nextJobClass: GenerateReferencesJob::class
        );
    }
}
