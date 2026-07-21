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

        if (config('demo.enabled', false)) {
            config([
                'queue.default' => env('DEMO_QUEUE_CONNECTION', 'sync'),
                'cache.default' => env('DEMO_CACHE_DRIVER', 'file'),
                'session.driver' => env('DEMO_SESSION_DRIVER', 'file'),
                'filesystems.default' => 'local',
            ]);
        }

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
            fn (): string => $this->getFilamentCustomStyles().$this->getDemoModeBadge()
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

    /**
     * Get Floating Session Badge HTML & JS for Demo Mode.
     */
    private function getDemoModeBadge(): string
    {
        if (! config('demo.enabled', false)) {
            return '';
        }

        $expiresAt = session('demo_expires_at', time() + 3600);

        return <<<HTML
            <div id="demo-session-badge" style="position: fixed; bottom: 24px; left: 24px; z-index: 9999; font-family: ui-sans-serif, system-ui, -apple-system, sans-serif; pointer-events: auto;">
                <div style="background: rgba(15, 23, 42, 0.92); backdrop-filter: blur(12px); border: 1px solid rgba(59, 130, 246, 0.35); box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.6), 0 0 15px rgba(16, 185, 129, 0.2); border-radius: 14px; padding: 12px 16px; display: flex; align-items: center; gap: 14px; color: #f8fafc;">
                    <div style="position: relative; display: flex; align-items: center; justify-content: center;">
                        <div style="position: absolute; width: 12px; height: 12px; border-radius: 50%; background-color: #10b981; opacity: 0.75; animation: demo-ping 1.5s cubic-bezier(0, 0, 0.2, 1) infinite;"></div>
                        <div style="width: 10px; height: 10px; border-radius: 50%; background-color: #10b981;"></div>
                    </div>
                    <div>
                        <div style="font-weight: 700; font-size: 11px; color: #38bdf8; letter-spacing: 0.6px; text-transform: uppercase; display: flex; align-items: center; gap: 6px;">
                            <span>DEMO MODU</span>
                            <span style="color: #64748b;">•</span>
                            <span style="color: #cbd5e1; font-weight: 500;">İzole SQLite</span>
                        </div>
                        <div style="font-size: 12px; color: #94a3b8; margin-top: 2px; display: flex; align-items: center; gap: 4px;">
                            Oturum Süresi: <span id="demo-session-timer" style="font-weight: 700; color: #34d399; font-family: monospace;">--:--</span>
                        </div>
                    </div>
                </div>
            </div>
            <style>
                @keyframes demo-ping {
                    75%, 100% {
                        transform: scale(2.2);
                        opacity: 0;
                    }
                }
            </style>
            <script>
                (function() {
                    var expiresAt = {$expiresAt};
                    function updateDemoTimer() {
                        var now = Math.floor(Date.now() / 1000);
                        var remaining = expiresAt - now;
                        var timerEl = document.getElementById('demo-session-timer');
                        if (!timerEl) return;
                        if (remaining <= 0) {
                            timerEl.innerText = 'Süre Doldu (Yenileniyor)';
                            window.location.reload();
                            return;
                        }
                        var mins = Math.floor(remaining / 60);
                        var secs = remaining % 60;
                        timerEl.innerText = (mins < 10 ? '0' : '') + mins + ':' + (secs < 10 ? '0' : '') + secs;
                    }
                    updateDemoTimer();
                    setInterval(updateDemoTimer, 1000);
                })();
            </script>
        HTML;
    }
}
