<?php

namespace App\Jobs;

use App\Enums\AnalysisStatus;
use App\Models\Analysis;
use App\Services\ReportService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateReportJob implements ShouldQueue
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
    public function handle(ReportService $reportService): void
    {
        try {
            $reportService->generateReports($this->analysis);

            $this->analysis->update([
                'status' => AnalysisStatus::COMPLETED,
                'completed_at' => now(),
            ]);

        } catch (\Exception $e) {
            $this->analysis->update([
                'status' => AnalysisStatus::FAILED,
                'error' => '[report] '.$e->getMessage(),
                'completed_at' => now(),
            ]);
        }
    }
}
