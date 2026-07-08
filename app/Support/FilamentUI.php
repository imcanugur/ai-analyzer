<?php

namespace App\Support;

use App\Enums\MediaType;
use App\Enums\SubmissionStatus;
use Carbon\Carbon;
use Filament\Actions\Action as FormAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;

class FilamentUI
{
    /**
     * Get base64 Data URL for a media file.
     */
    public static function getBase64DataUrl(mixed $media): string
    {
        if (! $media) {
            return '';
        }

        try {
            $disk = Storage::disk($media->disk);
            if ($disk->exists($media->path)) {
                $content = $disk->get($media->path);
                $mime = $media->mime ?? 'application/octet-stream';

                // Add charset to plain text files for proper browser rendering inside iframes
                if (str_starts_with($mime, 'text/')) {
                    return 'data:'.$mime.';charset=utf-8;base64,'.base64_encode($content);
                }

                return 'data:'.$mime.';base64,'.base64_encode($content);
            }
        } catch (\Exception $e) {
            // Fallback
        }

        return $media->url;
    }

    /**
     * Get base64 Data URL for a report file.
     */
    public static function getReportBase64DataUrl(mixed $report): string
    {
        if (! $report || ! isset($report->metadata['path'])) {
            return '';
        }

        try {
            $disk = Storage::disk(config('filesystems.default'));
            if ($disk->exists($report->metadata['path'])) {
                $content = $disk->get($report->metadata['path']);

                return 'data:text/html;charset=utf-8;base64,'.base64_encode($content);
            }
        } catch (\Exception $e) {
            // Fallback
        }

        return $report->metadata['url'] ?? Storage::disk(config('filesystems.default'))->url($report->metadata['path']);
    }

    /**
     * Render a consistent horizontal media card.
     */
    public static function mediaCard(mixed $media): HtmlString
    {
        return new HtmlString(view('filament.components.media-card', ['media' => $media])->render());
    }

    /**
     * Render a consistent status pill badge.
     */
    public static function statusBadge(mixed $status): HtmlString
    {
        return new HtmlString(view('filament.components.status-badge', ['status' => $status])->render());
    }

    /**
     * Render relative and absolute timestamps.
     */
    public static function relativeTime(mixed $time): HtmlString
    {
        return new HtmlString(view('filament.components.relative-time', ['time' => $time])->render());
    }


    /**
     * Reusable Attachment Management Section.
     */
    public static function attachmentSection(string $fieldName = 'file'): Section
    {
        return Section::make('Attachment')
            ->schema([
                Hidden::make('replace_file')
                    ->default(false),

                FileUpload::make($fieldName)
                    ->required(fn (string $operation, $get): bool => $operation === 'create' || $get('replace_file') === true)
                    ->visible(fn (string $operation, $get, $record): bool => $operation === 'create' ||
                        ! $record?->media()->exists() ||
                        $get('replace_file') === true
                    )
                    ->acceptedFileTypes([
                        'image/*',
                        'audio/*',
                        'video/*',
                        'application/pdf',
                        'application/msword',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                        'application/vnd.ms-excel',
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        'application/vnd.ms-powerpoint',
                        'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                        'text/plain',
                        'text/csv',
                        'text/html',
                        'text/css',
                        'text/javascript',
                        'application/json',
                        'application/xml',
                        'text/xml',
                    ])
                    ->validationMessages([
                        'mimetypes' => 'The selected file format is not supported. Please upload a valid document, image, audio, video, or source code file.',
                    ])
                    ->disk('local')
                    ->directory('temp-uploads')
                    ->maxSize(51200) // 50MB limit
                    ->columnSpanFull(),

                Placeholder::make('attached_file')
                    ->label('File Details')
                    ->hidden(fn ($get, $record) => ! $record?->media()->exists() || $get('replace_file') === true)
                    ->content(fn ($record) => self::mediaCard($record?->media()->first()))
                    ->columnSpanFull(),

                Actions::make([
                    FormAction::make('download_attachment')
                        ->label('Download File')
                        ->color('primary')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->action(function ($record) {
                            if ($record && $record->media()->exists()) {
                                $media = $record->media()->first();

                                return Storage::disk($media->disk)->download($media->path, $media->original_name);
                            }
                        }),

                    FormAction::make('change_attachment')
                        ->label('Change File')
                        ->color('warning')
                        ->icon('heroicon-o-pencil')
                        ->visible(fn (string $operation) => $operation === 'edit')
                        ->action(fn ($set) => $set('replace_file', true)),
                ])
                    ->hidden(fn ($get, $record) => ! $record?->media()->exists() || $get('replace_file') === true)
                    ->columnSpanFull(),
            ]);
    }


    /**
     * Reusable Status TextColumn for Tables.
     */
    public static function statusColumn(string $name = 'status'): TextColumn
    {
        return TextColumn::make($name)
            ->html()
            ->formatStateUsing(fn ($state) => self::statusBadge($state))
            ->sortable();
    }

    /**
     * Reusable Created At TextColumn for Tables.
     */
    public static function createdAtColumn(string $name = 'created_at'): TextColumn
    {
        return TextColumn::make($name)
            ->dateTime()
            ->sortable()
            ->toggleable(isToggledHiddenByDefault: true);
    }


    /**
     * Reusable Multi-Select Status Filter for Tables.
     */
    public static function statusFilter(string $name = 'status', string $enumClass = SubmissionStatus::class): SelectFilter
    {
        return SelectFilter::make($name)
            ->multiple()
            ->options(
                collect($enumClass::cases())
                    ->mapWithKeys(fn ($status) => [$status->value => ucfirst($status->value)])
                    ->toArray()
            );
    }


    /**
     * Reusable Date Range Filter for Tables.
     */
    public static function dateRangeFilter(string $fieldName = 'created_at', string $label = 'Created At'): Filter
    {
        return Filter::make($fieldName)
            ->form([
                DatePicker::make($fieldName.'_from')
                    ->label($label.' From'),
                DatePicker::make($fieldName.'_until')
                    ->label($label.' Until'),
            ])
            ->query(function ($query, array $data) use ($fieldName) {
                return $query
                    ->when(
                        $data[$fieldName.'_from'],
                        fn ($query, $date) => $query->whereDate($fieldName, '>=', $date),
                    )
                    ->when(
                        $data[$fieldName.'_until'],
                        fn ($query, $date) => $query->whereDate($fieldName, '<=', $date),
                    );
            })
            ->indicateUsing(function (array $data) use ($fieldName, $label): array {
                $indicators = [];
                if ($data[$fieldName.'_from'] ?? null) {
                    $indicators[$fieldName.'_from'] = $label.' from '.Carbon::parse($data[$fieldName.'_from'])->toFormattedDateString();
                }
                if ($data[$fieldName.'_until'] ?? null) {
                    $indicators[$fieldName.'_until'] = $label.' until '.Carbon::parse($data[$fieldName.'_until'])->toFormattedDateString();
                }

                return $indicators;
            });
    }


    /**
     * Render a consistent horizontal report card.
     */
    public static function reportCard(mixed $report): HtmlString
    {
        return new HtmlString(view('filament.components.report-card', ['report' => $report])->render());
    }
}
