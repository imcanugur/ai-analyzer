<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\SubmissionRepositoryInterface;
use App\DTO\CreateSubmissionDTO;
use App\Enums\SubmissionStatus;
use App\Models\Submission;
use Filament\Notifications\Notification;

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
        $submission = $this->submissionRepository->create([
            'user_id' => $dto->userId,
            'title' => $dto->title,
            'description' => $dto->description,
            'status' => SubmissionStatus::PENDING,
            'metadata' => $dto->metadata,
            'submitted_at' => now(),
        ]);

        // Send database notification to the user
        $user = $submission->user;
        if ($user) {
            Notification::make()
                ->title('Submission Uploaded')
                ->body("Your manuscript '{$submission->title}' has been successfully uploaded and queued.")
                ->icon('heroicon-o-document-arrow-up')
                ->color('success')
                ->sendToDatabase($user);
        }

        return $submission;
    }
}
