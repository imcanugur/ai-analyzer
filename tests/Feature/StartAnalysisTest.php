<?php

namespace Tests\Feature;

use App\Actions\StartAnalysisAction;
use App\DTO\StartAnalysisDTO;
use App\Enums\AnalysisStatus;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StartAnalysisTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that an analysis record can be successfully started for a submission.
     */
    public function test_analysis_can_be_started_with_pending_status(): void
    {
        \Illuminate\Support\Facades\Queue::fake();

        // 1. Create a user and a submission
        $user = User::factory()->create();
        $submission = Submission::factory()->create([
            'user_id' => $user->id,
            'title' => 'Test Submission',
            'description' => 'A submission for testing analysis',
        ]);

        // 2. Prepare analysis request data
        $data = [
            'submission' => $submission,
            'type' => 'document',
            'category' => 'academic',
            'provider' => 'ollama',
            'engine' => 'llm',
            'model' => 'gemma',
            'version' => '1.0',
            'config' => ['temperature' => 0.7],
            'metadata' => ['env' => 'testing'],
        ];

        $dto = StartAnalysisDTO::fromArray($data);

        // 3. Execute the Action
        /** @var StartAnalysisAction $action */
        $action = app(StartAnalysisAction::class);
        $analysis = $action->execute($dto);

        // 4. Assertions
        $this->assertNotNull($analysis);
        $this->assertEquals($submission->id, $analysis->submission_id);
        $this->assertEquals('document', $analysis->type);
        $this->assertEquals('academic', $analysis->category);
        $this->assertEquals('ollama', $analysis->provider);
        $this->assertEquals('llm', $analysis->engine);
        $this->assertEquals('gemma', $analysis->model);
        $this->assertEquals('1.0', $analysis->version);
        $this->assertEquals(AnalysisStatus::PENDING, $analysis->status);
        $this->assertEquals(['temperature' => 0.7], $analysis->config);
        $this->assertEquals(['env' => 'testing'], $analysis->metadata);

        // 5. Verify it exists in the database
        $this->assertDatabaseHas('analyses', [
            'id' => $analysis->id,
            'submission_id' => $submission->id,
            'type' => 'document',
            'status' => 'pending',
        ]);
    }
}
