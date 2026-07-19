<?php

namespace Tests\Feature;

use App\Actions\StartAnalysisAction;
use App\DTO\StartAnalysisDTO;
use App\Enums\AnalysisStatus;
use App\Enums\MediaType;
use App\Jobs\StartAnalysisJob;
use App\Models\Media;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class QueueAnalysisTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that starting an analysis dispatches the StartAnalysisJob.
     */
    public function test_analysis_dispatches_start_analysis_job(): void
    {
        Queue::fake();

        $user = User::factory()->create();
        $submission = Submission::factory()->create(['user_id' => $user->id]);

        $dto = StartAnalysisDTO::fromArray([
            'submission' => $submission,
            'type' => 'document',
        ]);

        app(StartAnalysisAction::class)->execute($dto);

        Queue::assertPushed(StartAnalysisJob::class);
    }

    /**
     * Test the entire queue pipeline execution with sync connection.
     */
    public function test_entire_queue_pipeline_executes_successfully(): void
    {
        // 1. Force queue connection to sync for synchronous execution of jobs
        config(['queue.default' => 'sync']);
        Storage::fake('local');

        // Mock Ollama API responses for all AI stages
        Http::fake([
            '*/api/generate' => Http::response([
                'model' => 'gemma2',
                'response' => 'AI generated response for testing.',
                'prompt_eval_count' => 10,
                'eval_count' => 20,
            ], 200),
        ]);

        $user = User::factory()->create();
        $submission = Submission::factory()->create(['user_id' => $user->id]);

        // Create dummy media file
        $fileName = 'test_document.txt';
        $fileContent = 'Hello World! This is a test file for extraction.';
        $path = Storage::disk('local')->putFileAs('temp-uploads', UploadedFile::fake()->createWithContent($fileName, $fileContent), $fileName);

        $media = Media::create([
            'mediable_type' => Submission::class,
            'mediable_id' => $submission->id,
            'disk' => 'local',
            'path' => $path,
            'url' => Storage::disk('local')->url($path),
            'mime' => 'text/plain',
            'size' => strlen($fileContent),
            'original_name' => $fileName,
            'extension' => 'txt',
            'type' => MediaType::DOCUMENT,
        ]);

        // 2. Trigger the action
        $dto = StartAnalysisDTO::fromArray([
            'submission' => $submission,
            'type' => 'document',
        ]);

        $analysis = app(StartAnalysisAction::class)->execute($dto);

        // 3. Assertions on the Analysis Model
        $analysis->refresh();
        $this->assertEquals(AnalysisStatus::COMPLETED, $analysis->status);
        $this->assertNotNull($analysis->started_at);
        $this->assertNotNull($analysis->completed_at);
        $this->assertNull($analysis->error);

        // 4. Assertions on stage results
        $stages = [
            'extract',
            'summary',
            'grammar',
            'references',
            'similarity',
            'reviewer',
        ];

        foreach ($stages as $stage) {
            $result = $analysis->results()->where('stage', $stage)->first();
            $this->assertNotNull($result, "AnalysisResult for stage [{$stage}] should exist.");
            $this->assertEquals(AnalysisStatus::COMPLETED, $result->status);
        }

        // 5. Verify extract stage has original text
        $extractResult = $analysis->results()->where('stage', 'extract')->first();
        $this->assertEquals($fileContent, $extractResult->payload['text']);

        // 6. Verify AI stages have generated content
        $summaryResult = $analysis->results()->where('stage', 'summary')->first();
        $this->assertEquals('AI generated response for testing.', $summaryResult->payload['text']);
        $this->assertEquals(30, $summaryResult->tokens); // 10 + 20
    }
}
