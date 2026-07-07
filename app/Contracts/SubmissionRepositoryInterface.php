<?php

namespace App\Contracts;

use App\Models\Submission;

interface SubmissionRepositoryInterface
{
    /**
     * Create a submission.
     */
    public function create(array $attributes): Submission;

    /**
     * Find a submission by UUID.
     */
    public function find(string $id): ?Submission;

    /**
     * Update a submission.
     */
    public function update(string $id, array $attributes): bool;

    /**
     * Delete a submission by UUID.
     */
    public function delete(string $id): bool;
}
