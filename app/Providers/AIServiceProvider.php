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
            return $app->make(\App\AI\Providers\ClusterAIProvider::class);
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
