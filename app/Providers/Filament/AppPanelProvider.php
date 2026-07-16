<?php

namespace App\Providers\Filament;

use AdriaanZon\FilamentPasskeys\FilamentPasskeysPlugin;
use AdriaanZon\FilamentPasskeys\PasskeyAuthentication;
use App\Filament\Pages\Auth\Login;
use App\Filament\Pages\Auth\Register;
use App\Filament\Pages\Auth\RequestPasswordReset;
use App\Filament\Pages\Auth\ResetPassword;
use App\Filament\Pages\Dashboard;
use App\Filament\Widgets\AccountWidget;
use App\Models\User;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use DutchCodingCompany\FilamentDeveloperLogins\FilamentDeveloperLoginsPlugin;
use Eloquage\FilamentHorizon\FilamentHorizonPlugin;
use Eloquage\FilamentHorizon\Widgets\StatsOverview;
use Eloquage\FilamentHorizon\Widgets\WorkersWidget;
use Eloquage\FilamentHorizon\Widgets\WorkloadWidget;
use Emuniq\FilamentBrowserNotifications\BrowserNotificationsPlugin;
use Filament\Actions\Action;
use Filament\Auth\MultiFactor\App\AppAuthentication;
use Filament\Auth\MultiFactor\Email\EmailAuthentication;
use Filament\Enums\DatabaseNotificationsPosition;
use Filament\Enums\GlobalSearchPosition;
use Filament\Enums\UserMenuPosition;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Platform;
use Filament\Support\Enums\Width;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AppPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('app')
            ->path('app')
            ->maxContentWidth(Width::Full)
            ->databaseNotifications(position: DatabaseNotificationsPosition::Sidebar)
            ->databaseNotificationsPolling('10s')
            ->login(Login::class)
            ->registration(Register::class)
            ->passwordReset(
                RequestPasswordReset::class,
                ResetPassword::class
            )
            ->emailVerification()
            ->profile(isSimple: false)
            ->multiFactorAuthentication([
                AppAuthentication::make()
                    ->recoverable()
                    ->recoveryCodeCount(8)
                    ->regenerableRecoveryCodes(false)
                    ->codeWindow(4),
                EmailAuthentication::make()
                    ->codeExpiryMinutes(4),
                PasskeyAuthentication::make()->managementOnly(),
            ], isRequired: true)
            ->plugins([
                FilamentShieldPlugin::make(),
                FilamentHorizonPlugin::make(),
                FilamentPasskeysPlugin::make()->passwordlessLogin(),
                BrowserNotificationsPlugin::make()
                    ->promptDelay(5)
                    ->dismissCooldownDays(14)
                    ->profileSection(true),
                FilamentDeveloperLoginsPlugin::make()
                    ->enabled(app()->environment('local'))
                    ->users(fn () => User::pluck('email', 'name')->toArray()),
            ])
            ->userMenu(position: UserMenuPosition::Sidebar)
            ->userMenuItems([
                'profile' => fn (Action $action) => $action->label('Edit profile'),
                'logout' => fn (Action $action) => $action->label('Log out'),
            ])
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
                FilamentInfoWidget::class,
                StatsOverview::class,
                WorkloadWidget::class,
                WorkersWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ], isPersistent: true)
            ->authMiddleware([
                Authenticate::class,
            ], isPersistent: true)
            ->databaseTransactions(true)
            ->unsavedChangesAlerts(true)
            ->sidebarCollapsibleOnDesktop(true)
            ->spa(hasPrefetching: true)
            ->globalSearch(position: GlobalSearchPosition::Sidebar)
            ->globalSearchDebounce('750ms')
            ->globalSearchKeyBindings(['command+k', 'ctrl+k'])
            ->globalSearchFieldSuffix(fn (): ?string => match (Platform::detect()) {
                Platform::Windows, Platform::Linux => 'CTRL+K',
                Platform::Mac => '⌘K',
                default => null,
            });
    }
}
