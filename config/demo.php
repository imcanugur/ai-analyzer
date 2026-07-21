<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Demo Mode Status
    |--------------------------------------------------------------------------
    |
    | When enabled, each session receives a dedicated, temporary SQLite DB in
    | storage/demo_dbs. Real AI calls are swapped with FakeAIProvider.
    |
    */
    'enabled' => (bool) env('DEMO_MODE', false),

    /*
    |--------------------------------------------------------------------------
    | Storage Directory for Demo Databases
    |--------------------------------------------------------------------------
    */
    'storage_path' => storage_path('demo_dbs'),

    /*
    |--------------------------------------------------------------------------
    | Session Duration (Minutes)
    |--------------------------------------------------------------------------
    */
    'session_duration_minutes' => (int) env('DEMO_SESSION_DURATION_MINUTES', 60),

    /*
    |--------------------------------------------------------------------------
    | Fake AI Delay Simulation (Seconds)
    |--------------------------------------------------------------------------
    */
    'fake_ai_delay_seconds' => (int) env('DEMO_FAKE_AI_DELAY', 3),
];
