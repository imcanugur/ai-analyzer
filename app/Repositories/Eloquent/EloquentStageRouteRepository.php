<?php

namespace App\Repositories\Eloquent;

use App\Contracts\StageRouteRepositoryInterface;
use App\Models\StageRoute;
use Illuminate\Support\Collection;

class EloquentStageRouteRepository implements StageRouteRepositoryInterface
{
    public function all(): Collection
    {
        return StageRoute::with('node')->orderBy('sort_order', 'asc')->get();
    }

    public function getActiveOrdered(): Collection
    {
        return StageRoute::with('node')
            ->active()
            ->ordered()
            ->get();
    }

    public function findByStage(string $stage): ?StageRoute
    {
        return StageRoute::where('stage', $stage)->first();
    }

    public function updateOrCreate(string $stage, array $attributes): StageRoute
    {
        return StageRoute::updateOrCreate(
            ['stage' => $stage],
            $attributes
        );
    }
}
