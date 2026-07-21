<?php

namespace App\Actions;

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

            $file = $dto->file;
            if (config('demo.enabled', false) && ! $file) {
                $tempPath = tempnam(sys_get_temp_dir(), 'demo_upload');
                file_put_contents($tempPath, 'Demo content for analysis.');
                $file = new \Illuminate\Http\UploadedFile(
                    $tempPath,
                    'demo_manuscript.txt',
                    'text/plain',
                    null,
                    true
                );
            }

            // 2. Upload file and create media polymorphic link
            $disk = config('filesystems.default', 'r2');
            $media = $this->mediaService->createMedia(
                model: $submission,
                file: $file,
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
