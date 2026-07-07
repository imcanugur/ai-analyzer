<?php

namespace App\Filament\Resources\SubmissionResource\Pages;

use App\Filament\Resources\SubmissionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSubmission extends EditRecord
{
    protected static string $resource = SubmissionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    /**
     * Handle the record update process and manage media replacement.
     */
    protected function handleRecordUpdate(\Illuminate\Database\Eloquent\Model $record, array $data): \Illuminate\Database\Eloquent\Model
    {
        return \Illuminate\Support\Facades\DB::transaction(function () use ($record, $data) {
            // 1. Update the title and description
            $record->update([
                'title' => $data['title'],
                'description' => $data['description'],
            ]);

            // 2. If the user requested to replace/change the file
            if (!empty($data['replace_file'])) {
                $mediaService = app(\App\Services\MediaService::class);
                
                $oldMedia = $record->media()->first();
                if ($oldMedia) {
                    $mediaService->delete($oldMedia);
                }

                // If a new file is uploaded, create the new media record
                if (!empty($data['file'])) {
                    $disk = config('filesystems.default', 'r2');
                    $mediaService->createMedia(
                        model: $record,
                        file: $data['file'],
                        disk: $disk,
                        sourceDisk: 'local'
                    );
                }
            }

            return $record;
        });
    }
}
