<?php

namespace App\Providers;

use App\AI\Contracts\AIProviderInterface;
use App\AI\Providers\ClaudeProvider;
use App\AI\Providers\OllamaProvider;
use Illuminate\Support\ServiceProvider;

class AIServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(AIProviderInterface::class, function ($app) {
            $driver = config('ai.default', 'ollama');

            return match ($driver) {
                'ollama' => new OllamaProvider,
                'claude' => new ClaudeProvider,
                default => throw new \InvalidArgumentException("Unsupported AI provider driver: {$driver}"),
            };
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
