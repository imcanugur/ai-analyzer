<?php

declare(strict_types=1);

namespace App\Providers;

use App\Contracts\AnalysisRepositoryInterface;
use App\Contracts\AnalysisResultRepositoryInterface;
use App\Contracts\MediaRepositoryInterface;
use App\Contracts\MediaTypeResolver;
use App\Contracts\NodeRepositoryInterface;
use App\Contracts\NotificationRepositoryInterface;
use App\Contracts\NotificationServiceInterface;
use App\Contracts\PathGenerator;
use App\Contracts\SettingRepositoryInterface;
use App\Contracts\StageRouteRepositoryInterface;
use App\Contracts\SubmissionRepositoryInterface;
use App\Contracts\UserRepositoryInterface;
use App\Repositories\Eloquent\EloquentAnalysisRepository;
use App\Repositories\Eloquent\EloquentAnalysisResultRepository;
use App\Repositories\Eloquent\EloquentMediaRepository;
use App\Repositories\Eloquent\EloquentNodeRepository;
use App\Repositories\Eloquent\EloquentNotificationRepository;
use App\Repositories\Eloquent\EloquentSettingRepository;
use App\Repositories\Eloquent\EloquentStageRouteRepository;
use App\Repositories\Eloquent\EloquentSubmissionRepository;
use App\Repositories\Eloquent\EloquentUserRepository;
use App\Services\NotificationService;
use App\Services\PromptService;
use App\Support\DefaultMediaTypeResolver;
use App\Support\DefaultPathGenerator;
use Filament\Support\Facades\FilamentView;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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

        $this->app->bind(
            UserRepositoryInterface::class,
            EloquentUserRepository::class
        );

        $this->app->bind(
            NotificationRepositoryInterface::class,
            EloquentNotificationRepository::class
        );

        $this->app->bind(
            NotificationServiceInterface::class,
            NotificationService::class
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

        \Illuminate\Support\Facades\Event::listen(
            'eloquent.created: ' . \App\Models\DatabaseNotification::class,
            \Emuniq\FilamentBrowserNotifications\Listeners\SendWebPushOnDatabaseNotification::class
        );

        DB::listen(function ($query) {
            Log::info('[SQL] '.$query->sql.' | Bindings: '.json_encode($query->bindings));
        });

        // Fix Filament notifications z-index conflicts and sidebar overlap
        FilamentView::registerRenderHook(
            'panels::body.end',
            fn (): string => '
                <style>
                    /* Target only the database notifications drawer to avoid breaking other modals */
                    #database-notifications {
                        z-index: 99999 !important;
                        overflow-x: hidden !important;
                    }
                    #database-notifications .fi-modal-window {
                        overflow-x: hidden !important;
                        max-width: 100vw !important;
                        width: 28rem !important;
                    }
                    #database-notifications div {
                        overflow-x: hidden !important;
                    }
                    @media (max-width: 640px) {
                        #database-notifications .fi-modal-window {
                            width: 100vw !important;
                        }
                    }
                    body.overflow-hidden {
                        overflow-x: hidden !important;
                    }
                </style>
            ',
        );
    }
}
