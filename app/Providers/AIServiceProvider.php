<?php

declare(strict_types=1);

namespace App\Providers;

use App\AI\Contracts\AIProviderInterface;
use App\AI\Providers\ClusterAIProvider;
use Illuminate\Support\ServiceProvider;

class AIServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(AIProviderInterface::class, function ($app) {
            return $app->make(ClusterAIProvider::class);
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
