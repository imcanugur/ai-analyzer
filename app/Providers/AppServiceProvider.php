<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            \App\Contracts\PathGenerator::class,
            \App\Support\DefaultPathGenerator::class
        );

        $this->app->bind(
            \App\Contracts\MediaTypeResolver::class,
            \App\Support\DefaultMediaTypeResolver::class
        );

        $this->app->bind(
            \App\Contracts\SubmissionRepositoryInterface::class,
            \App\Repositories\Eloquent\EloquentSubmissionRepository::class
        );

        $this->app->bind(
            \App\Contracts\MediaRepositoryInterface::class,
            \App\Repositories\Eloquent\EloquentMediaRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
