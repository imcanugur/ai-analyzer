<?php

namespace App\Filament\Resources\NodeResource\Pages;

use App\Contracts\NodeRepositoryInterface;
use App\Filament\Resources\NodeResource;
use App\Services\AIClusterService;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListNodes extends ListRecords
{
    protected static string $resource = NodeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('check_all_health')
                ->label('Check All Nodes')
                ->icon('heroicon-o-arrow-path')
                ->color('success')
                ->action(function (AIClusterService $clusterService, NodeRepositoryInterface $nodeRepository) {
                    $nodes = $nodeRepository->all();
                    $onlineCount = 0;
                    foreach ($nodes as $node) {
                        if ($clusterService->checkNodeHealth($node)) {
                            $onlineCount++;
                        }
                    }
                    Notification::make()
                        ->title('Cluster Health Checked')
                        ->body("{$onlineCount} of ".$nodes->count().' nodes are online.')
                        ->success()
                        ->send();
                }),
        ];
    }
}
