<?php

namespace App\AI\Cluster\LoadBalancers;

use App\AI\Cluster\Contracts\LoadBalancer;
use App\Models\Node;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class RoundRobin implements LoadBalancer
{
    /**
     * Select a node using Round Robin algorithm.
     */
    public function select(Collection $nodes, string $capability): Node
    {
        $nodes = $nodes->values();
        $cacheKey = 'cluster:load_balancer:round_robin:'.md5($capability);
        $lastNodeId = Cache::get($cacheKey);

        $index = 0;
        if ($lastNodeId) {
            $lastIndex = $nodes->search(fn ($node) => $node->id === $lastNodeId);
            if ($lastIndex !== false) {
                $index = ($lastIndex + 1) % $nodes->count();
            }
        }

        $selected = $nodes->get($index);
        Cache::put($cacheKey, $selected->id, now()->addDays(1));

        return $selected;
    }
}
