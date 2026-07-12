<?php

namespace App\AI\Cluster\LoadBalancers;

use App\AI\Cluster\Contracts\LoadBalancer;
use App\Models\Node;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class Priority implements LoadBalancer
{
    /**
     * Select a node using Priority algorithm.
     */
    public function select(Collection $nodes, string $capability): Node
    {
        $maxPriority = $nodes->max('priority');
        $candidates = $nodes->where('priority', $maxPriority)->values();

        if ($candidates->count() === 1) {
            return $candidates->first();
        }

        // If there's a tie, apply Round Robin on those candidates
        $cacheKey = 'cluster:load_balancer:priority_rr:'.md5($capability);
        $lastNodeId = Cache::get($cacheKey);

        $index = 0;
        if ($lastNodeId) {
            $lastIndex = $candidates->search(fn ($node) => $node->id === $lastNodeId);
            if ($lastIndex !== false) {
                $index = ($lastIndex + 1) % $candidates->count();
            }
        }

        $selected = $candidates->get($index);
        Cache::put($cacheKey, $selected->id, now()->addDays(1));

        return $selected;
    }
}
