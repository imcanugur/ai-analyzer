<?php

namespace App\Providers;

use App\Contracts\AnalysisRepositoryInterface;
use App\Contracts\AnalysisResultRepositoryInterface;
use App\Contracts\MediaRepositoryInterface;
use App\Contracts\MediaTypeResolver;
use App\Contracts\NodeRepositoryInterface;
use App\Contracts\PathGenerator;
use App\Contracts\SettingRepositoryInterface;
use App\Contracts\StageRouteRepositoryInterface;
use App\Contracts\SubmissionRepositoryInterface;
use App\Repositories\Eloquent\EloquentAnalysisRepository;
use App\Repositories\Eloquent\EloquentAnalysisResultRepository;
use App\Repositories\Eloquent\EloquentMediaRepository;
use App\Repositories\Eloquent\EloquentNodeRepository;
use App\Repositories\Eloquent\EloquentSettingRepository;
use App\Repositories\Eloquent\EloquentStageRouteRepository;
use App\Repositories\Eloquent\EloquentSubmissionRepository;
use App\Services\PromptService;
use App\Support\DefaultMediaTypeResolver;
use App\Support\DefaultPathGenerator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            PathGenerator::class,
            DefaultPathGenerator::class
        );

        $this->app->bind(
            MediaTypeResolver::class,
            DefaultMediaTypeResolver::class
        );

        $this->app->bind(
            SubmissionRepositoryInterface::class,
            EloquentSubmissionRepository::class
        );

        $this->app->bind(
            MediaRepositoryInterface::class,
            EloquentMediaRepository::class
        );

        $this->app->bind(
            AnalysisRepositoryInterface::class,
            EloquentAnalysisRepository::class
        );

        $this->app->bind(
            AnalysisResultRepositoryInterface::class,
            EloquentAnalysisResultRepository::class
        );

        $this->app->bind(
            NodeRepositoryInterface::class,
            EloquentNodeRepository::class
        );

        $this->app->bind(
            SettingRepositoryInterface::class,
            EloquentSettingRepository::class
        );

        $this->app->bind(
            StageRouteRepositoryInterface::class,
            EloquentStageRouteRepository::class
        );

        $this->app->singleton(
            PromptService::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        config(['livewire.temporary_file_upload.disk' => 'local']);
    }
}
