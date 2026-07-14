<?php

namespace App\Filament\Resources;

use App\Enums\MediaType;
use App\Filament\Resources\SubmissionResource\Pages;
use App\Models\Submission;
use App\Support\FilamentUI;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

class SubmissionResource extends Resource
{
    protected static ?string $model = Submission::class;

    protected static ?string $recordTitleAttribute = 'title';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-duplicate';

    public static function form(Schema $form): Schema
    {
        return $form
            ->schema([
                self::generalInfoSection(),
                FilamentUI::attachmentSection(),
                self::metadataSection(),
                self::analysisResultsSection(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),

                FilamentUI::statusColumn(),
                FilamentUI::createdAtColumn(),
                self::submittedAtColumn(),
            ])
            ->filters([
                FilamentUI::statusFilter(),
                self::mediaTypeFilter(),
                FilamentUI::dateRangeFilter(),
            ])
            ->actions([
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
            'index' => Pages\ListSubmissions::route('/'),
            'create' => Pages\CreateSubmission::route('/create'),
            'view' => Pages\ViewSubmission::route('/{record}'),
            'edit' => Pages\EditSubmission::route('/{record}/edit'),
        ];
    }

    /**
     * Submission General Information Section.
     */
    public static function generalInfoSection(): Section
    {
        return Section::make('General Information')
            ->schema([
                TextInput::make('title')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),

                Textarea::make('description')
                    ->maxLength(65535)
                    ->rows(8)
                    ->columnSpanFull(),
            ]);
    }

    /**
     * Submission/Analysis Metadata Section.
     */
    public static function metadataSection(): Section
    {
        return Section::make('Submission Meta')
            ->schema([
                Placeholder::make('created_at')
                    ->label('Created')
                    ->content(fn ($record) => FilamentUI::relativeTime($record?->created_at)),

                Placeholder::make('status')
                    ->label('Status')
                    ->content(fn ($record) => FilamentUI::statusBadge($record?->status)),
            ])
            ->hidden(fn (string $operation): bool => $operation === 'create')
            ->columnSpanFull();
    }

    /**
     * Display AI analysis reports and results inside the view form.
     */
    public static function analysisResultsSection(): Section
    {
        return Section::make('AI Analysis & Reports')
            ->schema([
                Placeholder::make('analysis_results')
                    ->label('')
                    ->content(fn ($record) => new HtmlString(view('filament.components.analysis-results', ['record' => $record])->render())),
            ])
            ->visible(fn (string $operation) => $operation === 'view')
            ->columnSpanFull();
    }

    /**
     * Submitted At TextColumn for Tables.
     */
    public static function submittedAtColumn(string $name = 'submitted_at'): TextColumn
    {
        return TextColumn::make($name)
            ->dateTime()
            ->sortable();
    }

    /**
     * Multi-Select Attached File Type Filter for Tables.
     */
    public static function mediaTypeFilter(string $relationName = 'media', string $fieldName = 'type'): SelectFilter
    {
        return SelectFilter::make($relationName.'_'.$fieldName)
            ->label('File Type')
            ->multiple()
            ->options(
                collect(MediaType::cases())
                    ->mapWithKeys(fn ($type) => [$type->value => ucfirst($type->value)])
                    ->toArray()
            )
            ->query(function ($query, array $data) use ($relationName, $fieldName) {
                if (! empty($data['values'])) {
                    $query->whereHas($relationName, fn ($q) => $q->whereIn($fieldName, $data['values']));
                }
            });
    }
}
