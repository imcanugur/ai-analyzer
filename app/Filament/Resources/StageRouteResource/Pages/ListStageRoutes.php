<?php

namespace App\Filament\Resources\StageRouteResource\Pages;

use App\Filament\Resources\StageRouteResource;
use Filament\Resources\Pages\ListRecords;

class ListStageRoutes extends ListRecords
{
    protected static string $resource = StageRouteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Static route list, no create action
        ];
    }
}
