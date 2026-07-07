<?php

namespace App\Jobs;

use App\Enums\AnalysisStatus;
use App\Models\Analysis;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class StartAnalysisJob implements ShouldQueue
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
    public function handle(): void
    {
        // 1. Set status to processing & set started_at timestamp
        $this->analysis->update([
            'status' => AnalysisStatus::PROCESSING,
            'started_at' => now(),
        ]);

        // 2. Dispatch the text extraction job in the background
        ExtractTextJob::dispatch($this->analysis);
    }
}
