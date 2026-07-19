<?php

namespace App\Contracts;

use App\Models\StageRoute;
use Illuminate\Support\Collection;

interface StageRouteRepositoryInterface
{
    /**
     * Get all stage routes.
     *
     * @return Collection<int, StageRoute>
     */
    public function all(): Collection;

    /**
     * Get all active stage routes ordered by sort_order.
     *
     * @return Collection<int, StageRoute>
     */
    public function getActiveOrdered(): Collection;

    /**
     * Find a stage route by its stage name.
     */
    public function findByStage(string $stage): ?StageRoute;

    /**
     * Create or update a stage route.
     */
    public function updateOrCreate(string $stage, array $attributes): StageRoute;
}
