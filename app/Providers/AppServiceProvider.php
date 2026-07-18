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
use App\Models\DatabaseNotification;
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
use Emuniq\FilamentBrowserNotifications\Listeners\SendWebPushOnDatabaseNotification;
use Filament\Support\Facades\FilamentView;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * All of the container bindings that should be registered.
     *
     * @var array<class-string, class-string>
     */
    public array $bindings = [
        PathGenerator::class => DefaultPathGenerator::class,
        MediaTypeResolver::class => DefaultMediaTypeResolver::class,
        SubmissionRepositoryInterface::class => EloquentSubmissionRepository::class,
        MediaRepositoryInterface::class => EloquentMediaRepository::class,
        AnalysisRepositoryInterface::class => EloquentAnalysisRepository::class,
        AnalysisResultRepositoryInterface::class => EloquentAnalysisResultRepository::class,
        NodeRepositoryInterface::class => EloquentNodeRepository::class,
        SettingRepositoryInterface::class => EloquentSettingRepository::class,
        StageRouteRepositoryInterface::class => EloquentStageRouteRepository::class,
        UserRepositoryInterface::class => EloquentUserRepository::class,
        NotificationRepositoryInterface::class => EloquentNotificationRepository::class,
        NotificationServiceInterface::class => NotificationService::class,
    ];

    /**
     * All of the container singletons that should be registered.
     *
     * @var array<class-string, class-string>
     */
    public array $singletons = [
        PromptService::class => PromptService::class,
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Registered via $bindings and $singletons properties
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        config(['livewire.temporary_file_upload.disk' => 'local']);

        Event::listen(
            'eloquent.created: '.DatabaseNotification::class,
            SendWebPushOnDatabaseNotification::class
        );

        if (config('app.debug')) {
            DB::listen(function ($query) {
                Log::info('[SQL] '.$query->sql.' | Bindings: '.json_encode($query->bindings));
            });
        }

        FilamentView::registerRenderHook(
            'panels::body.end',
            fn (): string => $this->getFilamentCustomStyles()
        );
    }

    /**
     * Get custom CSS styles for Filament view.
     */
    private function getFilamentCustomStyles(): string
    {
        $user = auth()->user();
        $hideHorizonCss = '';

        if ($user && ! $user->can('ViewHorizon')) {
            $hideHorizonCss = '
                .fi-sidebar-item:has(a[href*="/app/horizon"]) {
                    display: none !important;
                }
            ';
        }

        return <<<HTML
            <style>
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
                {$hideHorizonCss}
            </style>
        HTML;
    }
}
