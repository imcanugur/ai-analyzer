<?php

namespace App\Filament\Resources\SubmissionResource\Pages;

use App\Actions\StartAnalysisAction;
use App\DTO\StartAnalysisDTO;
use App\Filament\Resources\SubmissionResource;
use App\Services\MediaService;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        return DB::transaction(function () use ($record, $data) {
            // 1. Update the title and description
            $record->update([
                'title' => $data['title'],
                'description' => $data['description'],
            ]);

            // 2. If the user requested to replace/change the file
            if (! empty($data['replace_file'])) {
                $mediaService = app(MediaService::class);

                $oldMedia = $record->media()->first();
                if ($oldMedia) {
                    $mediaService->delete($oldMedia);
                }

                // If a new file is uploaded, create the new media record
                if (! empty($data['file'])) {
                    $disk = config('filesystems.default', 'r2');
                    $media = $mediaService->createMedia(
                        model: $record,
                        file: $data['file'],
                        disk: $disk,
                        sourceDisk: 'local'
                    );

                    // Automatically start a new analysis for the updated file
                    app(StartAnalysisAction::class)->execute(new StartAnalysisDTO(
                        submission: $record,
                        type: $media->type->value
                    ));
                }
            }

            return $record;
        });
    }
}
