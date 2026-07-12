<?php

namespace App\AI\Cluster\LoadBalancers;

use App\AI\Cluster\Contracts\LoadBalancer;
use App\Models\Node;
use Illuminate\Support\Collection;

class Random implements LoadBalancer
{
    /**
     * Select a node using Random algorithm.
     */
    public function select(Collection $nodes, string $capability): Node
    {
        return $nodes->random();
    }
}
