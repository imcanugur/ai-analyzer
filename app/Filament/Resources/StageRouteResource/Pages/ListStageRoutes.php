<?php

namespace App\Filament\Resources\StageRouteResource\Pages;

use App\Filament\Resources\StageRouteResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListStageRoutes extends ListRecords
{
    protected static string $resource = StageRouteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
