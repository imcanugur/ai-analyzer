<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NodeResource\Pages;
use App\Models\Node;
use App\Services\AIClusterService;
use App\Support\FilamentUI;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

class NodeResource extends Resource
{
    protected static ?string $model = Node::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-server-stack';

    protected static ?string $navigationLabel = 'AI Cluster Nodes';

    protected static ?string $modelLabel = 'AI Node';

    public static function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Section::make('Node Connection')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),

                        Select::make('driver')
                            ->options([
                                'ollama' => 'Ollama',
                                'claude' => 'Claude (Anthropic)',
                            ])
                            ->required()
                            ->default('ollama'),

                        TextInput::make('endpoint')
                            ->required()
                            ->url()
                            ->default('http://localhost:11434')
                            ->maxLength(255),

                        TextInput::make('api_key')
                            ->label('API Key / Token')
                            ->password()
                            ->dehydrateStateUsing(fn ($state) => filled($state) ? $state : null)
                            ->dehydrated(fn ($state) => filled($state))
                            ->maxLength(255),
                    ]),

                Section::make('Routing & Capabilities')
                    ->schema([
                        TextInput::make('weight')
                            ->required()
                            ->numeric()
                            ->default(1)
                            ->minValue(1),

                        TextInput::make('priority')
                            ->required()
                            ->numeric()
                            ->default(1)
                            ->minValue(1),

                        TagsInput::make('capabilities')
                            ->placeholder('Add capability (e.g. qwen3:4b, summary)')
                            ->helperText('Capabilities can be model names (like qwen3:4b) or stages (like summary)'),
                    ]),

                Section::make('Metadata & Monitoring')
                    ->schema([
                        Placeholder::make('status')
                            ->content(fn ($record) => FilamentUI::statusBadge($record?->status)),

                        Placeholder::make('active_connections')
                            ->content(fn ($record) => $record?->active_connections ?? 0),

                        Placeholder::make('last_health_check_at')
                            ->content(fn ($record) => $record?->last_health_check_at ? FilamentUI::relativeTime($record->last_health_check_at) : 'Never'),

                        Placeholder::make('last_error')
                            ->content(fn ($record) => $record?->last_error ?? 'None')
                            ->columnSpanFull(),
                    ])
                    ->hidden(fn (string $operation): bool => $operation === 'create')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('name', 'asc')
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('driver')
                    ->badge()
                    ->color('gray')
                    ->sortable(),

                TextColumn::make('endpoint')
                    ->searchable(),

                TextColumn::make('status')
                    ->html()
                    ->formatStateUsing(fn ($state) => FilamentUI::statusBadge($state))
                    ->sortable(),

                TextColumn::make('capabilities')
                    ->badge()
                    ->color('primary')
                    ->separator(','),

                TextColumn::make('weight')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('priority')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('active_connections')
                    ->label('Connections')
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Action::make('check_health')
                    ->label('Check Health')
                    ->icon('heroicon-o-heart')
                    ->color('success')
                    ->action(function (Node $record, AIClusterService $clusterService) {
                        $isOnline = $clusterService->checkNodeHealth($record);
                        if ($isOnline) {
                            \Filament\Notifications\Notification::make()
                                ->title("Node '{$record->name}' is Online!")
                                ->success()
                                ->send();
                        } else {
                            \Filament\Notifications\Notification::make()
                                ->title("Node '{$record->name}' health check failed")
                                ->body($record->last_error)
                                ->danger()
                                ->send();
                        }
                    }),
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListNodes::route('/'),
            'create' => Pages\CreateNode::route('/create'),
            'view' => Pages\ViewNode::route('/{record}'),
            'edit' => Pages\EditNode::route('/{record}/edit'),
        ];
    }
}
