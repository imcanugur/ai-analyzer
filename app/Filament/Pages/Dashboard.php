<?php

namespace App\Filament\Pages;

use Eloquage\FilamentHorizon\Widgets\StatsOverview;
use Eloquage\FilamentHorizon\Widgets\WorkersWidget;
use Eloquage\FilamentHorizon\Widgets\WorkloadWidget;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Widgets\AccountWidget;
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
        return [
            AccountWidget::class,
            FilamentInfoWidget::class,
            ...((auth()->user()?->can('ViewHorizon') ?? false) ? [
                StatsOverview::class,
                WorkloadWidget::class,
                WorkersWidget::class,
            ] : []),
        ];
    }
}
