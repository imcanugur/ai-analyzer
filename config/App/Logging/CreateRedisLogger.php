<?php

namespace App\Logging;

use Monolog\Logger;

class CreateRedisLogger
{
    public function __invoke(array $config)
    {
        return new Logger('redis', [
            new RedisStringHandler(
                $config['key'] ?? 'logs',
                $config['ttl'] ?? 14
            ),
        ]);
    }
}
