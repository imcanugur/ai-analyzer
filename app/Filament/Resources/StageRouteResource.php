<?php

namespace App\Filament\Resources;

use App\Contracts\NodeRepositoryInterface;
use App\Filament\Resources\StageRouteResource\Pages;
use App\Models\StageRoute;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Http;

class StageRouteResource extends Resource
{
    protected static ?string $model = StageRoute::class;

    protected static ?string $recordTitleAttribute = 'stage';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-arrows-right-left';

    protected static ?string $navigationLabel = 'AI Stage Routing';

    protected static ?string $modelLabel = 'Stage Route';

    public static function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Section::make('Routing Rules')
                    ->schema([
                        TextInput::make('stage')
                            ->disabled()
                            ->required(),

                        Select::make('node_id')
                            ->label('Preferred Node')
                            ->relationship('node', 'name')
                            ->placeholder('Dynamic (Load Balancer)')
                            ->live()
                            ->nullable(),

                        Select::make('model')
                            ->label('Model')
                            ->required()
                            ->options(function (callable $get) {
                                $nodeId = $get('node_id');
                                $nodeRepository = app(NodeRepositoryInterface::class);

                                if ($nodeId) {
                                    $node = $nodeRepository->find($nodeId);
                                    if ($node) {
                                        if ($node->driver === 'ollama') {
                                            try {
                                                $endpoint = rtrim($node->endpoint, '/');
                                                $response = Http::timeout(3)->get("{$endpoint}/api/tags");
                                                if ($response->successful()) {
                                                    $data = $response->json();
                                                    $models = [];
                                                    if (isset($data['models']) && is_array($data['models'])) {
                                                        foreach ($data['models'] as $modelData) {
                                                            if (isset($modelData['name'])) {
                                                                $models[] = $modelData['name'];
                                                            }
                                                        }
                                                    }
                                                    if (! empty($models)) {
                                                        $nodeRepository->update($node->id, [
                                                            'capabilities' => $models,
                                                            'status' => 'online',
                                                            'last_health_check_at' => now(),
                                                            'last_error' => null,
                                                        ]);

                                                        return array_combine($models, $models);
                                                    }
                                                }
                                            } catch (\Exception $e) {
                                                Notification::make()
                                                    ->title('Failed to fetch models from node')
                                                    ->body($e->getMessage())
                                                    ->danger()
                                                    ->send();
                                            }
                                        }

                                        if (is_array($node->capabilities) && ! empty($node->capabilities)) {
                                            return array_combine($node->capabilities, $node->capabilities);
                                        }
                                    }

                                    return [];
                                }

                                $nodes = $nodeRepository->all();
                                $capabilities = $nodes->pluck('capabilities')
                                    ->flatten()
                                    ->unique()
                                    ->filter()
                                    ->toArray();

                                if (empty($capabilities)) {
                                    return [];
                                }

                                return array_combine($capabilities, $capabilities);
                            }),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('stage', 'asc')
            ->columns([
                TextColumn::make('stage')
                    ->badge()
                    ->color('primary')
                    ->sortable(),

                TextColumn::make('model')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('node.name')
                    ->label('Preferred Node')
                    ->placeholder('Dynamic (Load Balancer)')
                    ->sortable(),

                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                // No bulk actions for static route list
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStageRoutes::route('/'),
            'edit' => Pages\EditStageRoute::route('/{record}/edit'),
        ];
    }
}
