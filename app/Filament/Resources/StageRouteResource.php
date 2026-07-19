<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Contracts\NodeRepositoryInterface;
use App\Filament\Resources\StageRouteResource\Pages;
use App\Models\StageRoute;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Http;

class StageRouteResource extends Resource
{
    protected static ?string $model = StageRoute::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cpu-chip';

    protected static ?string $navigationLabel = 'DAG Pipeline Stages';

    protected static ?string $modelLabel = 'Pipeline Stage';

    public static function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Section::make('Pipeline Topology & Stage Identity')
                    ->schema([
                        TextInput::make('stage')
                            ->label('Stage Key (Slug)')
                            ->placeholder('e.g., sentiment_analysis')
                            ->required()
                            ->unique(ignoreRecord: true),

                        TextInput::make('name')
                            ->label('Stage Display Name')
                            ->placeholder('e.g., Sentiment & Tone Analysis')
                            ->required(),

                        TextInput::make('sort_order')
                            ->label('Execution Order / Weight')
                            ->numeric()
                            ->default(10)
                            ->required(),

                        Toggle::make('is_active')
                            ->label('Active Status')
                            ->default(true),

                        Select::make('dependencies')
                            ->label('Prerequisite Stage Dependencies (DAG Graph)')
                            ->multiple()
                            ->options(function (?StageRoute $record) {
                                return StageRoute::query()
                                    ->when($record, fn ($q) => $q->where('id', '!=', $record->id))
                                    ->pluck('name', 'stage')
                                    ->toArray();
                            })
                            ->placeholder('Select stages that must complete before this stage executes...')
                            ->columnSpanFull(),

                        Textarea::make('description')
                            ->label('Description')
                            ->placeholder('Brief overview of what this pipeline stage performs...')
                            ->columnSpanFull(),
                    ])->columns(2),

                Section::make('Model, Cluster Node & Hyperparameters')
                    ->schema([
                        Select::make('node_id')
                            ->label('Preferred Cluster Node')
                            ->relationship('node', 'name')
                            ->placeholder('Dynamic (Load Balancer)')
                            ->live()
                            ->nullable(),

                        Select::make('model')
                            ->label('Target AI Model')
                            ->required()
                            ->options(function (callable $get) {
                                $nodeId = $get('node_id');
                                $nodeRepository = app(NodeRepositoryInterface::class);

                                if ($nodeId) {
                                    $node = $nodeRepository->find($nodeId);
                                    if ($node && $node->driver === 'ollama') {
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
                                                    return array_combine($models, $models);
                                                }
                                            }
                                        } catch (\Exception $e) {
                                            // Fallback silently
                                        }
                                    }

                                    if (is_array($node?->capabilities) && ! empty($node->capabilities)) {
                                        return array_combine($node->capabilities, $node->capabilities);
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
                                    return ['gemma2' => 'gemma2', 'qwen2.5' => 'qwen2.5'];
                                }

                                return array_combine($capabilities, $capabilities);
                            }),

                        TextInput::make('temperature')
                            ->label('Temperature (0.00 - 1.00)')
                            ->numeric()
                            ->placeholder('0.20')
                            ->nullable(),

                        TextInput::make('max_tokens')
                            ->label('Max Output Tokens')
                            ->numeric()
                            ->placeholder('2048')
                            ->nullable(),

                        Select::make('output_format')
                            ->label('Expected Output Format')
                            ->options([
                                'json' => 'Structured JSON',
                                'markdown' => 'Markdown Report',
                                'text' => 'Plain Text',
                            ])
                            ->default('json')
                            ->required(),

                        Select::make('on_failure')
                            ->label('Failure Handling Policy')
                            ->options([
                                'skip' => 'Skip stage & continue pipeline (Resilient)',
                                'fail_pipeline' => 'Halt entire pipeline immediately (Critical)',
                            ])
                            ->default('skip')
                            ->required(),
                    ])->columns(2),

                Section::make('Prompt Templates & System Directives')
                    ->description('Leave empty to load fallback prompt template from resources/prompts/{stage}.md.')
                    ->schema([
                        Textarea::make('system_prompt')
                            ->label('Custom System Prompt (Optional)')
                            ->rows(3)
                            ->placeholder('Override system prompt directives for this specific stage...'),

                        MarkdownEditor::make('prompt_template')
                            ->label('Stage User Prompt Template')
                            ->helperText('Available placeholders: {{ text }} for document text, or {{ summary_output }} for prerequisite stage outputs.')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order', 'asc')
            ->columns([
                TextColumn::make('sort_order')
                    ->label('Order')
                    ->sortable(),

                TextColumn::make('stage')
                    ->label('Slug')
                    ->badge()
                    ->color('primary')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('name')
                    ->label('Stage Name')
                    ->searchable(),

                TextColumn::make('dependencies')
                    ->label('Dependencies')
                    ->badge()
                    ->color('warning')
                    ->placeholder('None (Root Stage)'),

                TextColumn::make('model')
                    ->label('Model')
                    ->sortable(),

                TextColumn::make('on_failure')
                    ->label('Failure Policy')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'fail_pipeline' => 'danger',
                        default => 'secondary',
                    }),

                ToggleColumn::make('is_active')
                    ->label('Active'),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStageRoutes::route('/'),
            'create' => Pages\CreateStageRoute::route('/create'),
            'edit' => Pages\EditStageRoute::route('/{record}/edit'),
        ];
    }
}
