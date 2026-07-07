<?php

namespace App\Repositories\Eloquent;

use App\Contracts\AnalysisRepositoryInterface;
use App\Models\Analysis;

class EloquentAnalysisRepository implements AnalysisRepositoryInterface
{
    public function create(array $attributes): Analysis
    {
        return Analysis::create($attributes);
    }

    public function find(string $id): ?Analysis
    {
        return Analysis::find($id);
    }

    public function update(string $id, array $attributes): bool
    {
        $analysis = $this->find($id);
        if (! $analysis) {
            return false;
        }

        return $analysis->update($attributes);
    }

    public function delete(string $id): bool
    {
        $analysis = $this->find($id);
        if (! $analysis) {
            return false;
        }

        return $analysis->delete();
    }
}
