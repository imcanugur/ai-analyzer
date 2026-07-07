<?php

namespace App\Support;

use App\Enums\SubmissionStatus;
use App\Enums\MediaType;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Actions\Action as FormAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Support\HtmlString;
use Carbon\Carbon;

class FilamentUI
{
    /**
     * Render a consistent horizontal media card with inline styles.
     */
    /**
     * Get base64 Data URL for a media file.
     */
    public static function getBase64DataUrl(mixed $media): string
    {
        if (!$media) {
            return '';
        }

        try {
            $disk = \Illuminate\Support\Facades\Storage::disk($media->disk);
            if ($disk->exists($media->path)) {
                $content = $disk->get($media->path);
                $mime = $media->mime ?? 'application/octet-stream';
                
                // Add charset to plain text files for proper browser rendering inside iframes
                if (str_starts_with($mime, 'text/')) {
                    return 'data:' . $mime . ';charset=utf-8;base64,' . base64_encode($content);
                }
                
                return 'data:' . $mime . ';base64,' . base64_encode($content);
            }
        } catch (\Exception $e) {
            // Fallback
        }

        return $media->url;
    }

    /**
     * Render a consistent horizontal media card with inline styles.
     */
    public static function mediaCard(mixed $media): HtmlString
    {
        if (!$media) {
            return new HtmlString('<div style="font-size: 14px; color: #6b7280; font-style: italic;">No file attached</div>');
        }

        $extension = strtolower($media->extension);
        $previewUrl = self::getBase64DataUrl($media);
        $previewHtml = '';

        if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'])) {
            $previewHtml = sprintf('<img src="%s" style="max-width: 100%%; max-height: 100%%; object-fit: contain; border-radius: 8px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);" />', e($previewUrl));
        } elseif (in_array($extension, ['pdf', 'txt', 'html', 'json', 'xml', 'sql', 'css', 'js', 'py', 'php'])) {
            $previewHtml = sprintf('<iframe src="%s" style="width: 100%%; height: 100%%; border: none; border-radius: 8px; background-color: #ffffff;"></iframe>', e($previewUrl));
        } elseif (in_array($extension, ['mp4', 'webm', 'ogv'])) {
            $previewHtml = sprintf('<video controls src="%s" style="max-width: 100%%; max-height: 100%%; border-radius: 8px;"></video>', e($previewUrl));
        } elseif (in_array($extension, ['mp3', 'wav', 'ogg', 'm4a'])) {
            $previewHtml = sprintf('<audio controls src="%s" style="width: 100%%; max-width: 500px;"></audio>', e($previewUrl));
        } else {
            $previewHtml = '<div style="text-align: center;"><div style="font-size: 48px; margin-bottom: 12px;">📦</div><h4 style="font-size: 16px; font-weight: 600; color: #111827; margin: 0;">Preview not available for this format</h4><p style="font-size: 12px; color: #6b7280; margin: 4px 0 0 0;">Please download the file or open it externally to view.</p></div>';
        }

        return new HtmlString(
            sprintf(
                '<div x-data="{ open: false }">' .
                    '<style>' .
                        '.modal-btn-text { display: inline-block; }' .
                        '@media (max-width: 640px) {' .
                            '.modal-btn-text { display: none !important; }' .
                            '.modal-header-actions { gap: 6px !important; }' .
                            '.modal-header-title { font-size: 13px !important; max-width: 140px !important; }' .
                            '.modal-header-container { padding: 12px 16px !important; }' .
                            '.modal-body-container { padding: 12px !important; }' .
                            '.modal-header-icon { margin-right: 0 !important; }' .
                        '}' .
                    '</style>' .
                    '<div style="display: flex; align-items: center; border: 1px solid #e5e7eb; border-radius: 12px; padding: 16px; background-color: #f9fafb; box-shadow: 0 1px 2px 0 rgba(0,0,0,0.05); gap: 16px;">' .
                        '<div style="width: 48px; height: 48px; border-radius: 8px; background-color: #eff6ff; color: #2563eb; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">' .
                            '<svg style="width: 24px; height: 24px;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">' .
                                '<path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />' .
                            '</svg>' .
                        '</div>' .
                        '<div style="flex-grow: 1; text-align: left; min-width: 0;">' .
                            '<h4 style="font-size: 14px; font-weight: 600; color: #111827; margin: 0 0 2px 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="%s">%s</h4>' .
                            '<p style="font-size: 12px; color: #6b7280; margin: 0;">%s • %s</p>' .
                        '</div>' .
                        '<button @click="open = true; $event.preventDefault();" style="cursor: pointer; display: inline-flex; align-items: center; justify-content: center; padding: 8px 16px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 12px; font-weight: 600; color: #2563eb; background-color: #ffffff; text-decoration: none; box-shadow: 0 1px 2px 0 rgba(0,0,0,0.05); transition: background-color 0.2s; flex-shrink: 0;" onmouseover="this.style.backgroundColor=\'#f3f4f6\'" onmouseout="this.style.backgroundColor=\'#ffffff\'">' .
                            '<svg style="width: 14px; height: 14px; margin-right: 6px;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">' .
                                '<path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />' .
                                '<path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />' .
                            '</svg>' .
                            'Preview' .
                        '</button>' .
                    '</div>' .
                    '<template x-teleport="body">' .
                        '<div x-show="open" ' .
                             'x-transition:enter="transition ease-out duration-300" ' .
                             'x-transition:enter-start="opacity-0" ' .
                             'x-transition:enter-end="opacity-100" ' .
                             'x-transition:leave="transition ease-in duration-200" ' .
                             'x-transition:leave-start="opacity-100" ' .
                             'x-transition:leave-end="opacity-0" ' .
                             'style="position: fixed; inset: 0; z-index: 99999; background-color: rgba(0, 0, 0, 0.6); backdrop-filter: blur(4px);" ' .
                             '@keydown.escape.window="open = false">' .
                             '<div @click.away="open = false" ' .
                                  'style="position: absolute; top: 50%%; left: 50%%; transform: translate(-50%%, -50%%); background-color: #ffffff; width: calc(100%% - 32px); max-width: 1000px; height: 85vh; border-radius: 16px; display: flex; flex-direction: column; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04); overflow: hidden;">' .
                                  '<div class="modal-header-container" style="padding: 16px 24px; border-bottom: 1px solid #e5e7eb; display: flex; align-items: center; justify-content: space-between; background-color: #ffffff; flex-shrink: 0; gap: 12px;">' .
                                      '<div style="min-width: 0; flex-grow: 1; text-align: left;">' .
                                          '<h3 class="modal-header-title" style="font-size: 16px; font-weight: 600; color: #111827; margin: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">%s</h3>' .
                                          '<p style="font-size: 12px; color: #6b7280; margin: 2px 0 0 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">%s • %s</p>' .
                                      '</div>' .
                                      '<div class="modal-header-actions" style="display: flex; align-items: center; gap: 8px; flex-shrink: 0;">' .
                                          '<a href="%s" target="_blank" style="display: inline-flex; align-items: center; justify-content: center; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 12px; font-weight: 600; color: #374151; background-color: #ffffff; text-decoration: none; box-shadow: 0 1px 2px 0 rgba(0,0,0,0.05);" title="Open in New Tab">' .
                                              '<svg class="modal-header-icon" style="width: 14px; height: 14px; margin-right: 6px; flex-shrink: 0;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>' .
                                              '<span class="modal-btn-text">Open External</span>' .
                                          '</a>' .
                                          '<button type="button" @click="document.querySelector(\'[data-download-trigger]\')?.click(); open = false;" style="cursor: pointer; display: inline-flex; align-items: center; justify-content: center; padding: 8px 12px; border: 1px solid #2563eb; border-radius: 8px; font-size: 12px; font-weight: 600; color: #ffffff; background-color: #2563eb; text-decoration: none; box-shadow: 0 1px 2px 0 rgba(0,0,0,0.05);" title="Download File">' .
                                              '<svg class="modal-header-icon" style="width: 14px; height: 14px; margin-right: 6px; flex-shrink: 0;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>' .
                                              '<span class="modal-btn-text">Download</span>' .
                                          '</button>' .
                                          '<button @click="open = false" style="padding: 8px; border-radius: 8px; border: 1px solid #d1d5db; background-color: #f9fafb; color: #4b5563; cursor: pointer; display: flex; align-items: center; justify-content: center;" title="Close Modal">' .
                                              '<svg style="width: 16px; height: 16px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>' .
                                          '</button>' .
                                      '</div>' .
                                  '</div>' .
                                  '<div class="modal-body-container" style="flex-grow: 1; padding: 24px; background-color: #f3f4f6; display: flex; align-items: center; justify-content: center; overflow: auto; min-height: 0;">' .
                                      '%s' .
                                  '</div>' .
                             '</div>' .
                        '</div>' .
                    '</template>' .
                '</div>',
                e($media->original_name),
                e($media->original_name),
                e(strtoupper($media->extension)),
                e(number_format($media->size / 1024, 2) . ' KB'),
                e($media->original_name),
                e(strtoupper($media->extension)),
                e(number_format($media->size / 1024, 2) . ' KB'),
                e($media->url),
                $previewHtml
            )
        );
    }

    /**
     * Render a consistent status pill badge.
     */
    public static function statusBadge(mixed $status): HtmlString
    {
        if (!$status) {
            return new HtmlString('-');
        }

        $value = is_object($status) && isset($status->value) ? $status->value : (string) $status;

        // Match common status colors (works for SubmissionStatus, AnalysisStatus, etc.)
        $styles = [
            'pending' => ['bg' => '#f3f4f6', 'text' => '#1f2937', 'dot' => '#4b5563'],
            'processing' => ['bg' => '#eff6ff', 'text' => '#1e40af', 'dot' => '#3b82f6'],
            'completed' => ['bg' => '#ecfdf5', 'text' => '#065f46', 'dot' => '#10b981'],
            'failed' => ['bg' => '#fef2f2', 'text' => '#991b1b', 'dot' => '#ef4444'],
            'cancelled' => ['bg' => '#f3f4f6', 'text' => '#1f2937', 'dot' => '#4b5563'],
        ];

        $matched = $styles[strtolower($value)] ?? ['bg' => '#f3f4f6', 'text' => '#1f2937', 'dot' => '#4b5563'];

        return new HtmlString(
            sprintf(
                '<span style="display: inline-flex; align-items: center; border-radius: 9999px; font-size: 12px; font-weight: 600; padding: 4px 10px; background-color: %s; color: %s; margin-top: 4px;">' .
                    '<span style="width: 6px; height: 6px; border-radius: 50%%; background-color: %s; margin-right: 6px;"></span>' .
                    '%s' .
                '</span>',
                $matched['bg'],
                $matched['text'],
                $matched['dot'],
                e(ucfirst($value))
            )
        );
    }

    /**
     * Render relative and absolute timestamps with a clock icon.
     */
    public static function relativeTime(mixed $time): HtmlString
    {
        if (!$time) {
            return new HtmlString('-');
        }

        $date = $time instanceof Carbon ? $time : Carbon::parse($time);

        return new HtmlString(
            sprintf(
                '<div style="display: flex; align-items: center; font-size: 13px; color: #4b5563; padding-top: 4px;">' .
                    '<svg style="width: 16px; height: 16px; margin-right: 6px; color: #9ca3af; flex-shrink: 0;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">' .
                        '<path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0 Z" />' .
                    '</svg>' .
                    '<span>%s <span style="color: #9ca3af; font-size: 11px; margin-left: 4px;">(%s)</span></span>' .
                '</div>',
                e($date->diffForHumans()),
                e($date->format('Y-m-d H:i'))
            )
        );
    }

    /**
     * Reusable General Information Section.
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
     * Reusable Attachment Management Section.
     */
    public static function attachmentSection(string $fieldName = 'file'): Section
    {
        return Section::make('Attachment')
            ->schema([
                \Filament\Forms\Components\Hidden::make('replace_file')
                    ->default(false),

                FileUpload::make($fieldName)
                    ->required(fn (string $operation, $get): bool => $operation === 'create' || $get('replace_file') === true)
                    ->visible(fn (string $operation, $get, $record): bool => 
                        $operation === 'create' || 
                        !$record?->media()->exists() || 
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
                    ->hidden(fn ($get, $record) => !$record?->media()->exists() || $get('replace_file') === true)
                    ->content(fn ($record) => self::mediaCard($record?->media()->first()))
                    ->columnSpanFull(),

                Actions::make([
                    FormAction::make('download_attachment')
                        ->label('Download File')
                        ->color('primary')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->extraAttributes(['data-download-trigger' => 'true']),

                    FormAction::make('change_attachment')
                        ->label('Change File')
                        ->color('warning')
                        ->icon('heroicon-o-pencil')
                        ->visible(fn (string $operation) => $operation === 'edit')
                        ->action(fn ($set) => $set('replace_file', true)),
                ])
                ->hidden(fn ($get, $record) => !$record?->media()->exists() || $get('replace_file') === true)
                ->columnSpanFull(),
            ]);
    }

    /**
     * Reusable Submission/Analysis Metadata Section.
     */
    public static function metadataSection(): Section
    {
        return Section::make('Submission Meta')
            ->schema([
                Placeholder::make('created_at')
                    ->label('Created')
                    ->content(fn ($record) => self::relativeTime($record?->created_at)),
                
                Placeholder::make('status')
                    ->label('Status')
                    ->content(fn ($record) => self::statusBadge($record?->status)),
            ])
            ->hidden(fn (string $operation): bool => $operation === 'create')
            ->columnSpanFull();
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
     * Reusable Submitted At TextColumn for Tables.
     */
    public static function submittedAtColumn(string $name = 'submitted_at'): TextColumn
    {
        return TextColumn::make($name)
            ->dateTime()
            ->sortable();
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
     * Reusable Multi-Select Attached File Type Filter for Tables.
     */
    public static function mediaTypeFilter(string $relationName = 'media', string $fieldName = 'type'): SelectFilter
    {
        return SelectFilter::make($relationName . '_' . $fieldName)
            ->label('File Type')
            ->multiple()
            ->options(
                collect(MediaType::cases())
                    ->mapWithKeys(fn ($type) => [$type->value => ucfirst($type->value)])
                    ->toArray()
            )
            ->query(function ($query, array $data) use ($relationName, $fieldName) {
                if (!empty($data['values'])) {
                    $query->whereHas($relationName, fn ($q) => $q->whereIn($fieldName, $data['values']));
                }
            });
    }

    /**
     * Reusable Date Range Filter for Tables.
     */
    public static function dateRangeFilter(string $fieldName = 'created_at', string $label = 'Created At'): Filter
    {
        return Filter::make($fieldName)
            ->form([
                DatePicker::make($fieldName . '_from')
                    ->label($label . ' From'),
                DatePicker::make($fieldName . '_until')
                    ->label($label . ' Until'),
            ])
            ->query(function ($query, array $data) use ($fieldName) {
                return $query
                    ->when(
                        $data[$fieldName . '_from'],
                        fn ($query, $date) => $query->whereDate($fieldName, '>=', $date),
                    )
                    ->when(
                        $data[$fieldName . '_until'],
                        fn ($query, $date) => $query->whereDate($fieldName, '<=', $date),
                    );
            })
            ->indicateUsing(function (array $data) use ($fieldName, $label): array {
                $indicators = [];
                if ($data[$fieldName . '_from'] ?? null) {
                    $indicators[$fieldName . '_from'] = $label . ' from ' . Carbon::parse($data[$fieldName . '_from'])->toFormattedDateString();
                }
                if ($data[$fieldName . '_until'] ?? null) {
                    $indicators[$fieldName . '_until'] = $label . ' until ' . Carbon::parse($data[$fieldName . '_until'])->toFormattedDateString();
                }
                return $indicators;
            });
    }
}
