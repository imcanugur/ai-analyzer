<?php

namespace App\AI\Cluster\Contracts;

use App\Models\Node;
use Illuminate\Support\Collection;

interface LoadBalancer
{
    /**
     * Select a node from the collection of candidates based on capability.
     *
     * @param  Collection<int, Node>  $nodes
     */
    public function select(Collection $nodes, string $capability): Node;
}
