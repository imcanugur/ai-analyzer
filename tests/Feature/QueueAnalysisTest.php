<?php

namespace Tests\Feature;

use App\Actions\StartAnalysisAction;
use App\DTO\StartAnalysisDTO;
use App\Models\Submission;
use App\Models\User;
use App\Models\Media;
use App\Enums\AnalysisStatus;
use App\Enums\AnalysisStage;
use Illuminate\Foundation\Testing\RefreshDatabase;
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

        Queue::assertDispatched(\App\Jobs\StartAnalysisJob::class);
    }

    /**
     * Test the entire queue pipeline execution with sync connection.
     */
    public function test_entire_queue_pipeline_executes_successfully(): void
    {
        // 1. Force queue connection to sync for synchronous execution of jobs
        config(['queue.default' => 'sync']);
        Storage::fake('local');

        $user = User::factory()->create();
        $submission = Submission::factory()->create(['user_id' => $user->id]);

        // Create dummy media file
        $fileName = 'test_document.txt';
        $fileContent = 'Hello World! This is a test file for extraction.';
        $path = Storage::disk('local')->putFileAs('temp-uploads', \Illuminate\Http\UploadedFile::fake()->createWithContent($fileName, $fileContent), $fileName);

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
            'type' => \App\Enums\MediaType::DOCUMENT,
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

        // 4. Assertions on the AnalysisResult Model
        $result = $analysis->results()->where('stage', AnalysisStage::EXTRACT->value)->first();
        $this->assertNotNull($result);
        $this->assertEquals(AnalysisStatus::COMPLETED, $result->status);
        $this->assertEquals($fileContent, $result->payload['text']);
        $this->assertEquals('default_parser', $result->metadata['extractor']);
        $this->assertEquals('text/plain', $result->metadata['mime_type']);
    }
}
