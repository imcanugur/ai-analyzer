<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Contracts\NotificationServiceInterface;
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
     * Get the tags that should be assigned to the job.
     *
     * @return array<int, string>
     */
    public function tags(): array
    {
        return [
            'analysis',
            'start',
            'analysis_id:'.$this->analysis->id,
            'submission_id:'.($this->analysis->submission_id ?? 'none'),
        ];
    }

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

        $user = $this->analysis->submission?->user;
        if ($user) {
            app(NotificationServiceInterface::class)->send(
                $user,
                'Analysis Started',
                "The AI evaluation pipeline has started for manuscript '{$this->analysis->submission->title}'.",
                'heroicon-o-play-circle',
                'info'
            );
        }

        // 2. Dispatch the text extraction job in the background
        ExtractTextJob::dispatch($this->analysis);
    }
}
