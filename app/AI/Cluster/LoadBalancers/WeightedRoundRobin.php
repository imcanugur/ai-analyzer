<?php

namespace App\AI\Cluster\LoadBalancers;

use App\AI\Cluster\Contracts\LoadBalancer;
use App\Models\Node;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class WeightedRoundRobin implements LoadBalancer
{
    /**
     * Select a node using Weighted Round Robin algorithm.
     */
    public function select(Collection $nodes, string $capability): Node
    {
        $expanded = [];
        foreach ($nodes as $node) {
            $weight = max(1, $node->weight);
            for ($i = 0; $i < $weight; $i++) {
                $expanded[] = $node;
            }
        }

        $expandedCollection = collect($expanded);
        $cacheKey = "cluster:load_balancer:weighted_round_robin:".md5($capability);
        $lastIndex = Cache::get($cacheKey, -1);

        $nextIndex = ($lastIndex + 1) % $expandedCollection->count();
        $selected = $expandedCollection->get($nextIndex);

        Cache::put($cacheKey, $nextIndex, now()->addDays(1));

        return $selected;
    }
}
