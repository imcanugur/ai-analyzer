<?php

namespace App\Filament\Imports;

use App\Models\Node;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class NodeImporter extends Importer
{
    protected static ?string $model = Node::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('driver')
                ->requiredMapping()
                ->rules(['required', 'in:ollama,claude']),
            ImportColumn::make('endpoint')
                ->requiredMapping()
                ->rules(['required', 'url']),
            ImportColumn::make('api_key')
                ->rules(['nullable', 'string']),
            ImportColumn::make('weight')
                ->numeric()
                ->rules(['nullable', 'integer', 'min:1']),
            ImportColumn::make('priority')
                ->numeric()
                ->rules(['nullable', 'integer', 'min:1']),
            ImportColumn::make('capabilities')
                ->rules(['nullable', 'string']),
        ];
    }

    public function resolveRecord(): ?Node
    {
        $node = new Node;
        $node->name = $this->data['name'] ?? null;
        $node->driver = $this->data['driver'] ?? 'ollama';
        $node->endpoint = $this->data['endpoint'] ?? 'http://localhost:11434';
        $node->api_key = $this->data['api_key'] ?? null;
        $node->weight = (int) ($this->data['weight'] ?? 1);
        $node->priority = (int) ($this->data['priority'] ?? 1);
        $node->status = 'offline';

        if (! empty($this->data['capabilities'])) {
            if (is_array($this->data['capabilities'])) {
                $node->capabilities = $this->data['capabilities'];
            } else {
                $node->capabilities = array_filter(array_map('trim', explode(',', $this->data['capabilities'])));
            }
        }

        return $node;
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your node import has completed and '.number_format($import->successful_rows).' '.str('row')->plural($import->successful_rows).' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' '.number_format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' failed to import.';
        }

        return $body;
    }
}
