<?php

namespace App\AI\Providers;

use App\AI\Cluster\NodeRouter;
use App\AI\Contracts\AIProviderInterface;
use App\AI\DTO\AIResponse;
use App\Contracts\NodeRepositoryInterface;
use App\Contracts\StageRouteRepositoryInterface;
use App\Models\Node;
use Illuminate\Support\Facades\Log;

class ClusterAIProvider implements AIProviderInterface
{
    protected NodeRouter $router;

    protected NodeRepositoryInterface $nodeRepository;

    protected StageRouteRepositoryInterface $routeRepository;

    public function __construct(
        NodeRouter $router,
        NodeRepositoryInterface $nodeRepository,
        StageRouteRepositoryInterface $routeRepository
    ) {
        $this->router = $router;
        $this->nodeRepository = $nodeRepository;
        $this->routeRepository = $routeRepository;
    }

    /**
     * Generate text completion using the cluster nodes.
     */
    public function generate(string $prompt, array $options = [], ?string $systemPrompt = null): AIResponse
    {
        $stage = $options['stage'] ?? null;
        $capability = $options['model'] ?? 'gemma2';
        $preferredNode = null;

        // Resolve capability and preferred node from the database stage route
        if ($stage) {
            $stageRoute = $this->routeRepository->findByStage($stage);
            if ($stageRoute) {
                $capability = $stageRoute->model;
                if ($stageRoute->node_id) {
                    $preferredNode = $this->nodeRepository->find($stageRoute->node_id);
                }
            }
        }

        $retries = (int) config('ai.cluster.retry', 3);
        $failover = (bool) config('ai.cluster.failover', true);

        Log::info('[ClusterAIProvider] Starting cluster routing.', [
            'stage' => $stage,
            'capability' => $capability,
            'preferred_node' => $preferredNode?->name,
            'retries' => $retries,
            'failover' => $failover,
        ]);

        $attemptedNodeIds = [];

        for ($attempt = 1; $attempt <= $retries; $attempt++) {
            $node = null;

            // 1. Try preferred node on first attempt if online and not tried yet
            if ($preferredNode && ! in_array($preferredNode->id, $attemptedNodeIds)) {
                if ($preferredNode->status === 'online') {
                    $node = $preferredNode;
                } else {
                    Log::warning("[ClusterAIProvider] Pinned node '{$preferredNode->name}' for stage '{$stage}' is offline. Falling back to load balancer.");
                    $preferredNode = null; // Clear so we use dynamic load balancing candidates next
                }
            }

            // 2. Dynamic load balancing fallback
            if (! $node) {
                $candidates = $this->router->getCandidates($capability, $attemptedNodeIds);

                if ($candidates->isEmpty()) {
                    $errorMsg = "No active cluster nodes support capability: '{$capability}'. Please configure active nodes in the database/Filament.";
                    Log::error("[ClusterAIProvider] {$errorMsg}");
                    throw new \RuntimeException($errorMsg);
                }

                $node = $this->router->selectNode($candidates, $capability);
            }

            $attemptedNodeIds[] = $node->id;

            Log::info("[ClusterAIProvider] Routing attempt {$attempt} to Node: {$node->name} ({$node->endpoint})");

            try {
                // Increment connection count atomically via repository
                $this->nodeRepository->incrementConnections($node->id);

                // Get dynamic provider for node
                $provider = $this->getProviderForNode($node, $capability);

                // Run request
                $response = $provider->generate($prompt, $options, $systemPrompt);

                // Success! Decorate response with node metadata
                return new AIResponse(
                    text: $response->text,
                    tokens: $response->tokens,
                    executionTime: $response->executionTime,
                    rawResponse: $response->rawResponse,
                    metadata: [
                        'node_id' => $node->id,
                        'node_name' => $node->name,
                        'node_endpoint' => $node->endpoint,
                        'model' => $capability,
                        'driver' => $node->driver,
                    ]
                );

            } catch (\Exception $e) {
                Log::error("[ClusterAIProvider] Node '{$node->name}' execution failed.", [
                    'node' => $node->name,
                    'error' => $e->getMessage(),
                ]);

                // Mark the node as offline via repository
                $this->nodeRepository->update($node->id, [
                    'status' => 'offline',
                    'last_error' => 'Execution failed: '.$e->getMessage(),
                ]);

                if (! $failover || $attempt === $retries) {
                    throw new \RuntimeException(
                        "AI execution failed on node '{$node->name}' and failover is exhausted: ".$e->getMessage(),
                        0,
                        $e
                    );
                }

                Log::warning('[ClusterAIProvider] Failing over to next available node.');
            } finally {
                // Decrement connection count atomically via repository
                $this->nodeRepository->decrementConnections($node->id);
            }
        }

        throw new \RuntimeException("AI execution failed. Retries exhausted for capability: {$capability}");
    }

    /**
     * Get configured provider instance for a specific node.
     */
    protected function getProviderForNode(Node $node, string $capability): AIProviderInterface
    {
        $timeout = (int) config('ai.cluster.timeout', 300);

        return match ($node->driver) {
            'ollama' => new OllamaProvider(
                endpoint: $node->endpoint,
                defaultModel: $capability,
                timeout: $timeout,
                apiKey: $node->api_key
            ),
            'claude' => new ClaudeProvider,
            default => throw new \InvalidArgumentException("Unsupported node driver: {$node->driver}"),
        };
    }
}
