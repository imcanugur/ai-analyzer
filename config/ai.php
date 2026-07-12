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

    /*
    |--------------------------------------------------------------------------
    | AI Cluster Settings
    |--------------------------------------------------------------------------
    |
    | Configuration options for the AI cluster load balancer and dynamic routing.
    |
    */
    'cluster' => [
        'enabled' => env('AI_CLUSTER_ENABLED', true),
        'load_balancer' => env('AI_CLUSTER_LOAD_BALANCER', 'round_robin'),
        'health_check_interval' => (int) env('AI_CLUSTER_HEALTH_CHECK_INTERVAL', 30),
        'retry' => (int) env('AI_CLUSTER_RETRY', 3),
        'timeout' => (int) env('AI_CLUSTER_TIMEOUT', 300),
        'failover' => (bool) env('AI_CLUSTER_FAILOVER', true),
    ],

];

