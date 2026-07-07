<?php

namespace App\Services;

use App\Contracts\SubmissionRepositoryInterface;
use App\DTO\CreateSubmissionDTO;
use App\Enums\SubmissionStatus;
use App\Models\Submission;

class SubmissionService
{
    public function __construct(
        protected SubmissionRepositoryInterface $submissionRepository
    ) {}

    /**
     * Create a submission record in database.
     */
    public function create(CreateSubmissionDTO $dto): Submission
    {
        return $this->submissionRepository->create([
            'user_id' => $dto->userId,
            'title' => $dto->title,
            'description' => $dto->description,
            'status' => SubmissionStatus::PENDING,
            'metadata' => $dto->metadata,
            'submitted_at' => now(),
        ]);
    }
}
