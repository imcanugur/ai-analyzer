<?php

use App\Enums\AnalysisStage;
use App\Jobs\GenerateGrammarJob;
use App\Jobs\GenerateReferencesJob;
use App\Jobs\GenerateReportJob;
use App\Jobs\GenerateReviewerJob;
use App\Jobs\GenerateSimilarityJob;

return [

    /*
    |--------------------------------------------------------------------------
    | Default AI Provider
    |--------------------------------------------------------------------------
    |
    | This option controls the default AI provider that will be used to resolve
    | prompts. You can choose any of the configured providers below.
    |
    | Supported: "ollama", "claude"
    |
    */
    'default' => env('AI_PROVIDER', 'ollama'),

    /*
    |--------------------------------------------------------------------------
    | AI Providers Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure all the AI providers supported by the application.
    | Standard options include Ollama (local/cloud), Claude (Anthropic), etc.
    |
    | Ollama: Set api_key to null for local mode, or set it for cloud mode.
    | Claude: Always requires an API key.
    |
    */
    'providers' => [

        'ollama' => [
            'api_key' => env('OLLAMA_API_KEY'),
            'endpoint' => env('OLLAMA_ENDPOINT', 'http://localhost:11434'),
            'default_model' => env('OLLAMA_DEFAULT_MODEL', 'gemma2'),
            'timeout' => env('OLLAMA_TIMEOUT', 60),
        ],

        'claude' => [
            'api_key' => env('CLAUDE_API_KEY', ''),
            'default_model' => env('CLAUDE_DEFAULT_MODEL', 'claude-sonnet-4-20250514'),
            'timeout' => env('CLAUDE_TIMEOUT', 120),
            'max_tokens' => env('CLAUDE_MAX_TOKENS', 4096),
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | AI Analysis Stage Pipeline
    |--------------------------------------------------------------------------
    |
    | Defines the sequence of execution for the background analysis stages.
    | Decouples the job classes by avoiding direct imports between them.
    |
    */
    'pipeline' => [
        AnalysisStage::SUMMARY->value => GenerateGrammarJob::class,
        AnalysisStage::GRAMMAR->value => GenerateReferencesJob::class,
        AnalysisStage::REFERENCES->value => GenerateSimilarityJob::class,
        AnalysisStage::SIMILARITY->value => GenerateReviewerJob::class,
        AnalysisStage::REVIEWER->value => GenerateReportJob::class,
    ],

];
