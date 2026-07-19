<?php

namespace Tests\Feature;

use App\Enums\AnalysisStatus;
use App\Jobs\GenerateReportJob;
use App\Models\Analysis;
use App\Models\AnalysisResult;
use App\Models\Submission;
use App\Models\User;
use App\Services\ReportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ReportGenerationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test ReportService generates JSON and PDF outputs and stores them.
     */
    public function test_report_service_generates_and_stores_reports(): void
    {
        Storage::fake('local');
        config(['filesystems.default' => 'local']);

        $user = User::factory()->create();
        $submission = Submission::factory()->create(['user_id' => $user->id]);

        $analysis = Analysis::create([
            'submission_id' => $submission->id,
            'type' => 'document',
            'status' => AnalysisStatus::PROCESSING,
            'model' => 'gemma2',
        ]);

        // Create dummy analysis results for stages
        AnalysisResult::create([
            'analysis_id' => $analysis->id,
            'stage' => 'extract',
            'status' => AnalysisStatus::COMPLETED,
            'payload' => ['text' => 'This is extracted content.'],
        ]);

        AnalysisResult::create([
            'analysis_id' => $analysis->id,
            'stage' => 'summary',
            'status' => AnalysisStatus::COMPLETED,
            'payload' => ['text' => 'This is a summary.'],
        ]);

        $service = app(ReportService::class);
        $reports = $service->generateReports($analysis);

        $this->assertArrayHasKey('json', $reports);
        $this->assertArrayHasKey('pdf', $reports);

        // Verify JSON report database entry and contents
        $this->assertDatabaseHas('media', [
            'mediable_type' => Analysis::class,
            'mediable_id' => $analysis->id,
            'mime' => 'application/json',
        ]);
        $jsonPath = $reports['json']->path;
        $savedJson = json_decode(Storage::disk('local')->get($jsonPath), true);
        $this->assertEquals('This is a summary.', $savedJson['stages']['summary']['text']);

        // Verify PDF report database entry and physical file
        $this->assertDatabaseHas('media', [
            'mediable_type' => Analysis::class,
            'mediable_id' => $analysis->id,
            'mime' => 'application/pdf',
        ]);

        $pdfPath = $reports['pdf']->path;
        Storage::disk('local')->assertExists($pdfPath);
    }

    /**
     * Test GenerateReportJob processes the reporting lifecycle and updates status.
     */
    public function test_generate_report_job_completes_analysis(): void
    {
        Storage::fake('local');
        config(['filesystems.default' => 'local']);

        $user = User::factory()->create();
        $submission = Submission::factory()->create(['user_id' => $user->id]);

        $analysis = Analysis::create([
            'submission_id' => $submission->id,
            'type' => 'document',
            'status' => AnalysisStatus::PROCESSING,
        ]);

        // Trigger job execution
        GenerateReportJob::dispatch($analysis);

        $analysis->refresh();

        $this->assertEquals(AnalysisStatus::COMPLETED, $analysis->status);
        $this->assertNotNull($analysis->completed_at);
        $this->assertNull($analysis->error);

        // Verify reports exist in media table
        $this->assertDatabaseHas('media', [
            'mediable_type' => Analysis::class,
            'mediable_id' => $analysis->id,
            'mime' => 'application/json',
        ]);
        $this->assertDatabaseHas('media', [
            'mediable_type' => Analysis::class,
            'mediable_id' => $analysis->id,
            'mime' => 'application/pdf',
        ]);
    }
}
