<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StageRouteResource\Pages;
use App\Models\StageRoute;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Actions\EditAction;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class StageRouteResource extends Resource
{
    protected static ?string $model = StageRoute::class;

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

                        TextInput::make('model')
                            ->required()
                            ->placeholder('e.g. qwen3:4b')
                            ->maxLength(255),

                        Select::make('node_id')
                            ->label('Preferred Node')
                            ->relationship('node', 'name')
                            ->placeholder('Dynamic (Load Balancer)')
                            ->nullable(),
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
