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

        $expiresAt = session('demo_expires_at', time() + 900);

        return <<<HTML
            <div id="demo-session-badge" class="demo-badge-container">
                <div class="demo-badge-card">
                    <div class="demo-status-pulse">
                        <div class="demo-ping-ring"></div>
                        <div class="demo-dot"></div>
                    </div>
                    <div class="demo-badge-content">
                        <div class="demo-badge-header">
                            <span class="demo-title">DEMO MODE</span>
                            <span class="demo-dot-sep">•</span>
                            <span class="demo-subtitle">Isolated SQLite</span>
                        </div>
                        <div class="demo-badge-timer-row">
                            <span class="demo-timer-label">Time Remaining:</span>
                            <span id="demo-session-timer" class="demo-timer-val">--:--</span>
                        </div>
                    </div>
                </div>
            </div>
            <style>
                .demo-badge-container {
                    position: fixed;
                    bottom: 20px;
                    right: 20px;
                    z-index: 99999;
                    font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
                    pointer-events: auto;
                    transition: transform 0.2s cubic-bezier(0.4, 0, 0.2, 1);
                }
                .demo-badge-container:hover {
                    transform: translateY(-2px);
                }
                .demo-badge-card {
                    background: rgba(15, 23, 42, 0.88);
                    backdrop-filter: blur(16px);
                    -webkit-backdrop-filter: blur(16px);
                    border: 1px solid rgba(56, 189, 248, 0.3);
                    box-shadow: 0 12px 35px -6px rgba(0, 0, 0, 0.7), 0 0 20px rgba(16, 185, 129, 0.15);
                    border-radius: 14px;
                    padding: 10px 16px;
                    display: flex;
                    align-items: center;
                    gap: 12px;
                    color: #f8fafc;
                }
                .demo-status-pulse {
                    position: relative;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    width: 14px;
                    height: 14px;
                }
                .demo-ping-ring {
                    position: absolute;
                    width: 14px;
                    height: 14px;
                    border-radius: 50%;
                    background-color: #10b981;
                    opacity: 0.75;
                    animation: demo-ping 1.6s cubic-bezier(0, 0, 0.2, 1) infinite;
                }
                .demo-dot {
                    width: 8px;
                    height: 8px;
                    border-radius: 50%;
                    background-color: #10b981;
                    box-shadow: 0 0 8px #10b981;
                }
                .demo-badge-header {
                    font-weight: 700;
                    font-size: 11px;
                    color: #38bdf8;
                    letter-spacing: 0.6px;
                    text-transform: uppercase;
                    display: flex;
                    align-items: center;
                    gap: 6px;
                    line-height: 1;
                }
                .demo-dot-sep {
                    color: #475569;
                }
                .demo-subtitle {
                    color: #94a3b8;
                    font-weight: 500;
                    text-transform: none;
                    letter-spacing: normal;
                }
                .demo-badge-timer-row {
                    font-size: 12px;
                    color: #cbd5e1;
                    margin-top: 4px;
                    display: flex;
                    align-items: center;
                    gap: 6px;
                    line-height: 1;
                }
                .demo-timer-val {
                    font-weight: 700;
                    color: #34d399;
                    font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
                    letter-spacing: 0.5px;
                }
                @keyframes demo-ping {
                    75%, 100% {
                        transform: scale(2.3);
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
                            timerEl.innerText = 'Expired (Refreshing...)';
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
