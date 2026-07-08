<?php

namespace App\Contracts;

use App\Models\Report;

interface ReportRepositoryInterface
{
    /**
     * Create a report record.
     */
    public function create(array $attributes): Report;

    /**
     * Find a report record by UUID.
     */
    public function find(string $id): ?Report;

    /**
     * Update a report record.
     */
    public function update(string $id, array $attributes): bool;

    /**
     * Delete a report record.
     */
    public function delete(string $id): bool;
}
