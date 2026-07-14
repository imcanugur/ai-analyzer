<?php

use Illuminate\Support\Str;

return [

    /*
    |--------------------------------------------------------------------------
    | Horizon Domain
    |--------------------------------------------------------------------------
    |
    | This is the subdomain where Horizon will be accessible from. If this
    | setting is null, Horizon will reside under the same domain as the
    | application. Otherwise, this value will be used for routing.
    |
    | Only uncomment this when domain configuration is needed.
    */

    // 'domain' => env('HORIZON_DOMAIN'),

    /*
    |--------------------------------------------------------------------------
    | Horizon Path
    |--------------------------------------------------------------------------
    |
    | This is the URI path where Horizon will be accessible from. Feel free
    | to change this path to anything you like. Note that the URI will not
    | affect the paths of its internal API routes.
    |
    */

    'path' => env('HORIZON_PATH', 'horizon'),

    /*
    |--------------------------------------------------------------------------
    | Horizon Redis Connection
    |--------------------------------------------------------------------------
    |
    | This is the name of the Redis connection that Horizon will use to store
    | its meta-information. The connection name must match one of the
    | connections defined in your application's "database" config file.
    |
    */

    'use' => 'default',

    /*
    |--------------------------------------------------------------------------
    | Horizon Prefix
    |--------------------------------------------------------------------------
    |
    | This prefix will be used when storing Horizon data in Redis. You may
    | change this prefix to prevent collision with other applications using
    | the same Redis server.
    |
    */

    'prefix' => env(
        'HORIZON_PREFIX',
        Str::slug(env('APP_NAME', 'laravel'), '_').'_horizon:'
    ),

    /*
    |--------------------------------------------------------------------------
    | Horizon Route Middleware
    |--------------------------------------------------------------------------
    |
    | These middleware will get attached onto each Horizon route, giving you
    | the chance to add your own middleware to this list or change any of
    | the existing middleware. Or, you can simply use the "web" group.
    |
    */

    'middleware' => ['web'],

    /*
    |--------------------------------------------------------------------------
    | Queue Wait Thresholds
    |--------------------------------------------------------------------------
    |
    | This option allows you to define how long (in seconds) a queue can wait
    | before it is considered to have a long wait time. You may configure
    | thresholds for any queue defined in your database configuration.
    |
    */

    'waits' => [
        'redis:default' => 60,
    ],

    /*
    |--------------------------------------------------------------------------
    | Job Trimming Times
    |--------------------------------------------------------------------------
    |
    | Here you can configure for how long (in minutes) Horizon will keep
    | completed jobs, pending jobs, and failed jobs in Redis. This allows
    | you to fine-tune the amount of Redis memory consumed by Horizon.
    |
    */

    'trim' => [
        'recent' => 60,
        'pending' => 60,
        'completed' => 60,
        'recent_failed' => 10080,
        'failed' => 10080,
    ],

    /*
    |--------------------------------------------------------------------------
    | Silenced Jobs
    |--------------------------------------------------------------------------
    |
    | Silence specific jobs from being logged to recent jobs list in Redis.
    | Silenced jobs will still be visible in the "failed jobs" list and
    | are still counted towards dashboard stats and queue wait time.
    |
    */

    'silenced' => [
        // \App\Jobs\ExampleJob::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Metrics Configuration
    |--------------------------------------------------------------------------
    |
    | Here you can configure how many jobs you would like to keep for metrics
    | graphing. This allows you to control how much historical data Horizon
    | will collect for queue wait times and job processing times.
    |
    */

    'metrics' => [
        'trim_days' => [
            'overall' => 7,
            'queue' => 7,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Fast Termination
    |--------------------------------------------------------------------------
    |
    | When this option is enabled, Horizon's termination process will not wait
    | for currently executing jobs to finish. Jobs will be terminated
    | quickly, which is ideal when running in auto-scaling VM environments.
    |
    */

    'fast_termination' => false,

    /*
    |--------------------------------------------------------------------------
    | Memory Limit
    |--------------------------------------------------------------------------
    |
    | This value is the maximum amount of memory (in megabytes) that a worker
    | process may consume before it is terminated and restarted. You should
    | set this value according to your server capabilities.
    |
    */

    'memory_limit' => 64,

    /*
    |--------------------------------------------------------------------------
    | Queue Environments
    |--------------------------------------------------------------------------
    |
    | Here you may define the queue environment configurations for Horizon.
    | These configurations will automatically be applied when the app is
    | running in the specified environment.
    |
    */

    'environments' => [
        'production' => [
            'supervisor-1' => [
                'connection' => 'redis',
                'queue' => ['default'],
                'balance' => 'auto',
                'autoScale' => true,
                'minProcesses' => 1,
                'maxProcesses' => 10,
                'tries' => 3,
                'timeout' => 300,
            ],
        ],

        'local' => [
            'supervisor-1' => [
                'connection' => 'redis',
                'queue' => ['default'],
                'balance' => 'simple',
                'processes' => 3,
                'tries' => 3,
                'timeout' => 300,
            ],
        ],
    ],
];
