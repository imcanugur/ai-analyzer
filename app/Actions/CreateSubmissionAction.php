<?php

namespace App\Actions;

use App\DTO\CreateSubmissionDTO;
use App\Models\Submission;
use App\Services\MediaService;
use App\Services\SubmissionService;
use Illuminate\Support\Facades\DB;

class CreateSubmissionAction
{
    public function __construct(
        protected SubmissionService $submissionService,
        protected MediaService $mediaService
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
            $this->mediaService->createMedia($submission, $dto->file, $disk);

            return $submission;
        });
    }
}
