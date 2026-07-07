<?php

namespace App\Actions;

use App\Actions\StartAnalysisAction;
use App\DTO\CreateSubmissionDTO;
use App\DTO\StartAnalysisDTO;
use App\Models\Submission;
use App\Services\MediaService;
use App\Services\SubmissionService;
use Illuminate\Support\Facades\DB;

class CreateSubmissionAction
{
    public function __construct(
        protected SubmissionService $submissionService,
        protected MediaService $mediaService,
        protected StartAnalysisAction $startAnalysisAction
    ) {}

    /**
     * Coordinate database transaction, submission creation, and media uploading.
     */
    public function execute(CreateSubmissionDTO $dto): Submission
    {
        return DB::transaction(function () use ($dto) {
            // 1. Create the submission
            $submission = $this->submissionService->create($dto);

            // 2. Upload file and create media polymorphic link
            $disk = config('filesystems.default', 'r2');
            $media = $this->mediaService->createMedia(
                model: $submission,
                file: $dto->file,
                disk: $disk,
                sourceDisk: 'local'
            );

            // 3. Automatically start analysis based on the media type
            $this->startAnalysisAction->execute(new StartAnalysisDTO(
                submission: $submission,
                type: $media->type->value
            ));

            return $submission;
        });
    }
}
