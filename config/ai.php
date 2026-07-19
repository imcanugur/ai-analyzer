<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | AI Cluster Settings
    |--------------------------------------------------------------------------
    |
    | Configuration options for the AI cluster load balancer and dynamic routing.
    | Pipeline execution is fully dynamic and managed via database StageRoute records.
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
