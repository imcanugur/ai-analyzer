<?php

namespace App\Contracts;

use App\Models\AnalysisResult;

interface AnalysisResultRepositoryInterface
{
    /**
     * Create an analysis result.
     */
    public function create(array $attributes): AnalysisResult;

    /**
     * Find an analysis result by UUID.
     */
    public function find(string $id): ?AnalysisResult;

    /**
     * Update an analysis result.
     */
    public function update(string $id, array $attributes): bool;

    /**
     * Delete an analysis result by UUID.
     */
    public function delete(string $id): bool;
}
