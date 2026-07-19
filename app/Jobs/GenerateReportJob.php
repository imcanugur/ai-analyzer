<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Enums\AnalysisStatus;
use App\Models\Analysis;
use App\Models\AnalysisResult;
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

            AnalysisResult::updateOrCreate(
                [
                    'analysis_id' => $this->analysis->id,
                    'stage' => 'report',
                ],
                [
                    'status' => AnalysisStatus::COMPLETED,
                    'payload' => ['text' => 'PDF and JSON reports generated successfully.'],
                ]
            );

            $this->analysis->update([
                'status' => AnalysisStatus::COMPLETED,
                'completed_at' => now(),
            ]);

            app(\App\Services\PipelineService::class)->runNextPendingStage($this->analysis);

        } catch (\Exception $e) {
            $this->analysis->update([
                'status' => AnalysisStatus::FAILED,
                'error' => '[report] '.$e->getMessage(),
                'completed_at' => now(),
            ]);
        }
    }
}
