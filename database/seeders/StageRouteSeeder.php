<?php

namespace Database\Seeders;

use App\Contracts\NodeRepositoryInterface;
use App\Contracts\StageRouteRepositoryInterface;
use Illuminate\Database\Seeder;

class StageRouteSeeder extends Seeder
{
    protected StageRouteRepositoryInterface $routeRepository;

    protected NodeRepositoryInterface $nodeRepository;

    public function __construct(
        StageRouteRepositoryInterface $routeRepository,
        NodeRepositoryInterface $nodeRepository
    ) {
        $this->routeRepository = $routeRepository;
        $this->nodeRepository = $nodeRepository;
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $nodes = $this->nodeRepository->all();

        $node1 = $nodes->first(fn ($n) => str_contains($n->endpoint, '11434'));
        $node2 = $nodes->first(fn ($n) => str_contains($n->endpoint, '11435'));
        $node3 = $nodes->first(fn ($n) => str_contains($n->endpoint, '11436'));

        $routes = [
            'summary' => ['model' => 'qwen3:4b', 'node_id' => $node1?->id],
            'grammar' => ['model' => 'gemma3:4b', 'node_id' => $node2?->id],
            'references' => ['model' => 'qwen3:4b', 'node_id' => $node1?->id],
            'similarity' => ['model' => 'qwen3:8b', 'node_id' => $node1?->id],
            'reviewer' => ['model' => 'mistral:7b', 'node_id' => $node3?->id],
            'plagiarism' => ['model' => 'qwen3:8b', 'node_id' => $node1?->id],
            'readability' => ['model' => 'gemma3:4b', 'node_id' => $node2?->id],
        ];

        foreach ($routes as $stage => $data) {
            $this->routeRepository->updateOrCreate($stage, $data);
        }
    }
}
