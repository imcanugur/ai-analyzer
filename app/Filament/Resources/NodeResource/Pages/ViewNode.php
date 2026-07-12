<?php

namespace App\Filament\Resources\NodeResource\Pages;

use App\Filament\Resources\NodeResource;
use Filament\Actions;
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
                ->action(function (\App\Services\AIClusterService $clusterService) {
                    $isOnline = $clusterService->checkNodeHealth($this->record);
                    if ($isOnline) {
                        \Filament\Notifications\Notification::make()
                            ->title("Node is Online!")
                            ->success()
                            ->send();
                    } else {
                        \Filament\Notifications\Notification::make()
                            ->title("Node health check failed")
                            ->body($this->record->last_error)
                            ->danger()
                            ->send();
                    }
                }),
            Actions\EditAction::make(),
        ];
    }
}
