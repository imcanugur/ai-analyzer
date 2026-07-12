<?php

namespace App\Filament\Resources\NodeResource\Pages;

use App\Filament\Resources\NodeResource;
use App\Services\AIClusterService;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewNode extends ViewRecord
{
    protected static string $resource = NodeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('check_health')
                ->label('Check Health')
                ->icon('heroicon-o-heart')
                ->color('success')
                ->action(function (AIClusterService $clusterService) {
                    $isOnline = $clusterService->checkNodeHealth($this->record);
                    if ($isOnline) {
                        Notification::make()
                            ->title('Node is Online!')
                            ->success()
                            ->send();
                    } else {
                        Notification::make()
                            ->title('Node health check failed')
                            ->body($this->record->last_error)
                            ->danger()
                            ->send();
                    }
                }),
            Actions\EditAction::make(),
        ];
    }
}
