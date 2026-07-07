<?php

namespace App\Logging;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Redis;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;
use Monolog\LogRecord;

class RedisStringHandler extends AbstractProcessingHandler
{
    protected string $baseKey;

    protected int $ttl;

    public function __construct(string $baseKey = 'logs', int $ttl = 14, $level = Logger::DEBUG, bool $bubble = true)
    {
        parent::__construct($level, $bubble);
        $this->baseKey = $baseKey;
        $this->ttl = $ttl * 24 * 60 * 60;
    }

    protected function write(array|LogRecord $record): void
    {
        $env = App::environment();
        $date = now()->format('Y-m-d:H');
        $level = strtolower($record['level_name']);

        $key = "{$this->baseKey}:{$env}:{$date}:{$level}";

        Redis::append($key, $record['formatted'].PHP_EOL);

        Redis::expire($key, $this->ttl);
    }
}
