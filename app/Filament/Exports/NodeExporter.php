<?php

namespace App\Filament\Exports;

use App\Models\Node;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Number;

class NodeExporter extends Exporter
{
    protected static ?string $model = Node::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('name')
                ->label('Name'),
            ExportColumn::make('driver')
                ->label('Driver'),
            ExportColumn::make('endpoint')
                ->label('Endpoint'),
            ExportColumn::make('weight')
                ->label('Weight'),
            ExportColumn::make('priority')
                ->label('Priority'),
            ExportColumn::make('status')
                ->label('Status'),
            ExportColumn::make('capabilities')
                ->label('Capabilities')
                ->state(fn ($record): string => is_array($record->capabilities) ? implode(', ', $record->capabilities) : ''),
            ExportColumn::make('active_connections')
                ->label('Active Connections'),
            ExportColumn::make('last_health_check_at')
                ->label('Last Health Check At'),
            ExportColumn::make('created_at')
                ->label('Created At'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your node export has completed and ' . Number::format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
