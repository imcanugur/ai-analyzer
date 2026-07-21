<?php

namespace App\AI\Cluster;

use App\AI\Cluster\Contracts\LoadBalancer;
use App\AI\Cluster\LoadBalancers\LeastConnections;
use App\AI\Cluster\LoadBalancers\Priority;
use App\AI\Cluster\LoadBalancers\Random;
use App\AI\Cluster\LoadBalancers\RoundRobin;
use App\AI\Cluster\LoadBalancers\WeightedRoundRobin;
use App\Contracts\NodeRepositoryInterface;
use App\Models\Node;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class NodeRouter
{
    protected NodeRepositoryInterface $nodeRepository;

    public function __construct(NodeRepositoryInterface $nodeRepository)
    {
        $this->nodeRepository = $nodeRepository;
    }

    /**
     * Get candidate nodes for the requested capability, optionally excluding some.
     *
     * @return Collection<int, Node>
     */
    public function getCandidates(string $capability, array $excludeNodeIds = []): Collection
    {
        $allOnlineNodes = $this->nodeRepository->getActiveNodes();

        if (! empty($excludeNodeIds)) {
            $allOnlineNodes = $allOnlineNodes->whereNotIn('id', $excludeNodeIds);
        }

        // Filter nodes that support the capability (either matches model name or stage name)
        return $allOnlineNodes->filter(function (Node $node) use ($capability) {
            $capabilities = $node->capabilities ?: [];

            // Perform case-insensitive match
            foreach ($capabilities as $cap) {
                if (strcasecmp($cap, $capability) === 0) {
                    return true;
                }
            }

            return false;
        });
    }

    /**
     * Select a node from the candidates using the configured load balancing strategy.
     *
     * @param  Collection<int, Node>  $candidates
     */
    public function selectNode(Collection $candidates, string $capability): Node
    {
        if ($candidates->isEmpty()) {
            throw new RuntimeException("No online candidate nodes available to route capability: {$capability}");
        }

        if ($candidates->count() === 1) {
            return $candidates->first();
        }

        $strategyName = config('ai.cluster.load_balancer', 'round_robin');
        $strategy = $this->resolveStrategy($strategyName);

        Log::info("[NodeRouter] Selecting node from {$candidates->count()} candidates using strategy '{$strategyName}' for capability '{$capability}'.");

        return $strategy->select($candidates, $capability);
    }

    /**
     * Resolve the load balancer strategy class.
     */
    protected function resolveStrategy(string $strategyName): LoadBalancer
    {
        return match ($strategyName) {
            'weighted_round_robin' => new WeightedRoundRobin,
            'least_connections' => new LeastConnections,
            'random' => new Random,
            'priority' => new Priority,
            'round_robin' => new RoundRobin,
            default => new RoundRobin,
        };
    }
}
