<?php

namespace App\Filament\Resources\SubmissionResource\Pages;

use App\Actions\CreateSubmissionAction;
use App\DTO\CreateSubmissionDTO;
use App\Filament\Resources\SubmissionResource;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateSubmission extends CreateRecord
{
    protected static string $resource = SubmissionResource::class;

    /**
     * Handle the record creation process by executing the CreateSubmissionAction.
     */
    protected function handleRecordCreation(array $data): Model
    {
        $data['user_id'] = \Filament\Facades\Filament::auth()->id() ?? auth()->id() ?? User::first()?->id;

        $dto = CreateSubmissionDTO::fromArray($data);

        return app(CreateSubmissionAction::class)->execute($dto);
    }
}
