<?php

namespace App\Contracts;

use App\Models\Analysis;

interface AnalysisRepositoryInterface
{
    /**
     * Create an analysis record.
     */
    public function create(array $attributes): Analysis;

    /**
     * Find an analysis record by UUID.
     */
    public function find(string $id): ?Analysis;

    /**
     * Update an analysis record.
     */
    public function update(string $id, array $attributes): bool;

    /**
     * Delete an analysis record by UUID.
     */
    public function delete(string $id): bool;
}
