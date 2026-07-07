<?php

namespace App\Repositories\Eloquent;

use App\Contracts\AnalysisResultRepositoryInterface;
use App\Models\AnalysisResult;

class EloquentAnalysisResultRepository implements AnalysisResultRepositoryInterface
{
    public function create(array $attributes): AnalysisResult
    {
        return AnalysisResult::create($attributes);
    }

    public function find(string $id): ?AnalysisResult
    {
        return AnalysisResult::find($id);
    }

    public function update(string $id, array $attributes): bool
    {
        $result = $this->find($id);
        if (! $result) {
            return false;
        }

        return $result->update($attributes);
    }

    public function delete(string $id): bool
    {
        $result = $this->find($id);
        if (! $result) {
            return false;
        }

        return $result->delete();
    }
}
