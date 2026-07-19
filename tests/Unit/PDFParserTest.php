<?php

namespace Tests\Unit;

use App\Contracts\AnalysisResultRepositoryInterface;
use App\Enums\AnalysisStatus;
use App\Enums\MediaType;
use App\Jobs\ExtractTextJob;
use App\Models\Analysis;
use App\Models\Media;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Smalot\PdfParser\Config as PdfConfig;
use Smalot\PdfParser\Parser;
use Tests\TestCase;

class PDFParserTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that the PDF configuration values are set correctly.
     */
    public function test_pdf_configuration_values_are_loaded(): void
    {
        $this->assertNotNull(config('pdf.font_space_limit'));
        // Verify default config fallback is -15
        $this->assertEquals(-15, config('pdf.font_space_limit'));
    }

    /**
     * Test that the parser is initialized with the configured font space limit.
     */
    public function test_parser_initialization_with_custom_config(): void
    {
        $fontSpaceLimit = -25;
        config(['pdf.font_space_limit' => $fontSpaceLimit]);

        $config = new PdfConfig;
        $config->setFontSpaceLimit(config('pdf.font_space_limit'));

        $this->assertEquals($fontSpaceLimit, $config->getFontSpaceLimit());

        $parser = new Parser([], $config);
        $this->assertInstanceOf(Parser::class, $parser);
    }

    /**
     * Test that the ExtractTextJob uses pdftotext when it is available and successful.
     */
    public function test_job_extracts_text_using_pdftotext_when_available(): void
    {
        Storage::fake('local');
        Process::fake([
            'pdftotext *' => Process::result('Mocked pdftotext output content.', 0),
        ]);

        // 1. Setup Models
        $user = User::factory()->create();
        $submission = Submission::factory()->create(['user_id' => $user->id]);

        $path = Storage::disk('local')->put('temp-uploads/document.pdf', 'dummy pdf content');

        $media = Media::create([
            'mediable_type' => Submission::class,
            'mediable_id' => $submission->id,
            'disk' => 'local',
            'path' => $path,
            'url' => Storage::disk('local')->url($path),
            'mime' => 'application/pdf',
            'size' => 100,
            'original_name' => 'document.pdf',
            'extension' => 'pdf',
            'type' => MediaType::DOCUMENT,
        ]);

        $analysis = Analysis::create([
            'submission_id' => $submission->id,
            'type' => 'document',
            'category' => 'academic',
            'provider' => 'ollama',
            'engine' => 'llm',
            'model' => 'gemma2',
            'status' => AnalysisStatus::PROCESSING,
        ]);

        // Prevent dispatching subsequent jobs
        Queue::fake();

        // 2. Execute the Job
        $job = new ExtractTextJob($analysis);
        $job->handle(app(AnalysisResultRepositoryInterface::class));

        // 3. Assertions
        $analysis->refresh();
        $result = $analysis->results()->where('stage', 'extract')->first();

        $this->assertNotNull($result);
        $this->assertEquals('Mocked pdftotext output content.', $result->payload['text']);

        // Assert pdftotext was invoked
        Process::assertRan(function ($process) {
            return str_contains($process->command()[0], 'pdftotext');
        });
    }

    /**
     * Test that the ExtractTextJob falls back to Smalot PdfParser when pdftotext fails.
     */
    public function test_job_falls_back_to_smalot_pdfparser_when_pdftotext_fails(): void
    {
        Storage::fake('local');
        // Fake process run returning exit code 1 (failure)
        Process::fake([
            'pdftotext *' => Process::result('', 1),
        ]);

        $user = User::factory()->create();
        $submission = Submission::factory()->create(['user_id' => $user->id]);

        // Write invalid PDF content so that Smalot parser fails and throws, which we can catch
        $path = Storage::disk('local')->put('temp-uploads/document.pdf', 'invalid pdf');

        $media = Media::create([
            'mediable_type' => Submission::class,
            'mediable_id' => $submission->id,
            'disk' => 'local',
            'path' => $path,
            'url' => Storage::disk('local')->url($path),
            'mime' => 'application/pdf',
            'size' => 11,
            'original_name' => 'document.pdf',
            'extension' => 'pdf',
            'type' => MediaType::DOCUMENT,
        ]);

        $analysis = Analysis::create([
            'submission_id' => $submission->id,
            'type' => 'document',
            'category' => 'academic',
            'provider' => 'ollama',
            'engine' => 'llm',
            'model' => 'gemma2',
            'status' => AnalysisStatus::PROCESSING,
        ]);

        Queue::fake();

        // 2. Execute the Job (this should fallback to Smalot, fail on 'invalid pdf', and mark analysis as FAILED)
        $job = new ExtractTextJob($analysis);
        $job->handle(app(AnalysisResultRepositoryInterface::class));

        // 3. Assertions
        $analysis->refresh();
        $this->assertEquals(AnalysisStatus::FAILED, $analysis->status);
        $this->assertNotNull($analysis->error);
        $this->assertStringContainsString('Unable to find PDF header', $analysis->error);
    }
}
