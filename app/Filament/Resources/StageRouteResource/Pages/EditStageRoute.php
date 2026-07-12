<?php

namespace App\Filament\Resources\StageRouteResource\Pages;

use App\Filament\Resources\StageRouteResource;
use Filament\Resources\Pages\EditRecord;

class EditStageRoute extends EditRecord
{
    protected static string $resource = StageRouteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // No custom actions needed
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
