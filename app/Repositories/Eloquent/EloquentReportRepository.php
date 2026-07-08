<?php

namespace App\Repositories\Eloquent;

use App\Contracts\ReportRepositoryInterface;
use App\Models\Report;

class EloquentReportRepository implements ReportRepositoryInterface
{
    /**
     * Create a report record.
     */
    public function create(array $attributes): Report
    {
        return Report::create($attributes);
    }

    /**
     * Find a report record by UUID.
     */
    public function find(string $id): ?Report
    {
        return Report::find($id);
    }

    /**
     * Update a report record.
     */
    public function update(string $id, array $attributes): bool
    {
        $report = $this->find($id);

        if (! $report) {
            return false;
        }

        return $report->update($attributes);
    }

    /**
     * Delete a report record.
     */
    public function delete(string $id): bool
    {
        $report = $this->find($id);

        if (! $report) {
            return false;
        }

        return $report->delete();
    }
}
