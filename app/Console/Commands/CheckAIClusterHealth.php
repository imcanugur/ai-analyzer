<?php

namespace App\Console\Commands;

use App\Contracts\NodeRepositoryInterface;
use App\Services\AIClusterService;
use Illuminate\Console\Command;

class CheckAIClusterHealth extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ai:cluster:health-check';

    /**
     * The description of the console command.
     *
     * @var string
     */
    protected $description = 'Perform a health check on all AI cluster nodes and update capabilities.';

    /**
     * Execute the console command.
     */
    public function handle(AIClusterService $clusterService, NodeRepositoryInterface $nodeRepository): void
    {
        $this->info('Starting AI Cluster Health Check...');
        $nodes = $nodeRepository->all();

        if ($nodes->isEmpty()) {
            $this->warn('No nodes registered in the database.');

            return;
        }

        foreach ($nodes as $node) {
            $this->info("Checking node: {$node->name} ({$node->endpoint})");

            $isOnline = $clusterService->checkNodeHealth($node);

            // Reload node data to display updated capabilities/errors correctly
            $node->refresh();

            if ($isOnline) {
                $capabilitiesStr = ! empty($node->capabilities) ? implode(', ', $node->capabilities) : 'none';
                $this->info("✔ Node '{$node->name}' is ONLINE. Capabilities: {$capabilitiesStr}");
            } else {
                $this->error("✘ Node '{$node->name}' is OFFLINE. Error: {$node->last_error}");
            }
        }

        $this->info('AI Cluster Health Check completed.');
    }
}
