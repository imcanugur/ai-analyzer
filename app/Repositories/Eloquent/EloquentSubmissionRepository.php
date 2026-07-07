<?php

namespace App\Repositories\Eloquent;

use App\Contracts\SubmissionRepositoryInterface;
use App\Models\Submission;

class EloquentSubmissionRepository implements SubmissionRepositoryInterface
{
    /**
     * Create a submission.
     */
    public function create(array $attributes): Submission
    {
        return Submission::create($attributes);
    }

    /**
     * Find a submission by UUID.
     */
    public function find(string $id): ?Submission
    {
        return Submission::find($id);
    }

    /**
     * Update a submission.
     */
    public function update(string $id, array $attributes): bool
    {
        $submission = $this->find($id);
        if (! $submission) {
            return false;
        }

        return $submission->update($attributes);
    }

    /**
     * Delete a submission by UUID.
     */
    public function delete(string $id): bool
    {
        $submission = $this->find($id);
        if (! $submission) {
            return false;
        }

        return $submission->delete();
    }
}
