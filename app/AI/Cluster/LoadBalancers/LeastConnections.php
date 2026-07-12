<?php

namespace App\AI\Cluster\LoadBalancers;

use App\AI\Cluster\Contracts\LoadBalancer;
use App\Models\Node;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class LeastConnections implements LoadBalancer
{
    /**
     * Select a node using Least Connections algorithm.
     */
    public function select(Collection $nodes, string $capability): Node
    {
        // Group by active_connections, get the group with the lowest connections
        $minConnections = $nodes->min('active_connections');
        $candidates = $nodes->where('active_connections', $minConnections)->values();

        if ($candidates->count() === 1) {
            return $candidates->first();
        }

        // If there's a tie, find candidates with the highest priority in that group
        $maxPriority = $candidates->max('priority');
        $bestCandidates = $candidates->where('priority', $maxPriority)->values();

        if ($bestCandidates->count() === 1) {
            return $bestCandidates->first();
        }

        // If still a tie, apply Round Robin on the best candidates
        $cacheKey = 'cluster:load_balancer:least_conn_rr:'.md5($capability);
        $lastNodeId = Cache::get($cacheKey);

        $index = 0;
        if ($lastNodeId) {
            $lastIndex = $bestCandidates->search(fn ($node) => $node->id === $lastNodeId);
            if ($lastIndex !== false) {
                $index = ($lastIndex + 1) % $bestCandidates->count();
            }
        }

        $selected = $bestCandidates->get($index);
        Cache::put($cacheKey, $selected->id, now()->addDays(1));

        return $selected;
    }
}
