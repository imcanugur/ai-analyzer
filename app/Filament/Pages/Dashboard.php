<?php

namespace App\Filament\Pages;

use Eloquage\FilamentHorizon\Widgets\StatsOverview;
use Eloquage\FilamentHorizon\Widgets\WorkersWidget;
use Eloquage\FilamentHorizon\Widgets\WorkloadWidget;
use Filament\Pages\Dashboard as BaseDashboard;
use App\Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;

class Dashboard extends BaseDashboard
{
    /**
     * Get the widgets that should be displayed on the dashboard page.
     *
     * @return array<class-string>
     */
    public function getWidgets(): array
    {
        $widgets = [
            WorkloadWidget::class => 'View:WorkloadWidget',
            WorkersWidget::class => 'View:WorkersWidget',
            StatsOverview::class => 'View:StatsOverview',
        ];

        return [
            AccountWidget::class,
            FilamentInfoWidget::class,
            ...array_keys(array_filter(
                $widgets,
                fn (string $permission): bool => auth()->user()?->can($permission) ?? false
            )),
        ];
    }
}
