<?php

namespace Database\Seeders;

use App\Contracts\NodeRepositoryInterface;
use Illuminate\Database\Seeder;

class NodeSeeder extends Seeder
{
    protected NodeRepositoryInterface $nodeRepository;

    public function __construct(NodeRepositoryInterface $nodeRepository)
    {
        $this->nodeRepository = $nodeRepository;
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if ($this->nodeRepository->all()->count() === 0) {
            $this->nodeRepository->create([
                'name' => 'Node-1 (Local Ollama)',
                'driver' => 'ollama',
                'endpoint' => 'http://localhost:11434',
                'api_key' => null,
                'status' => 'unknown',
                'capabilities' => ['qwen3:4b', 'qwen3:8b', 'gemma3:4b', 'mistral:7b'],
                'weight' => 1,
                'priority' => 1,
            ]);

            $this->nodeRepository->create([
                'name' => 'Node-2 (Ollama Port 11435)',
                'driver' => 'ollama',
                'endpoint' => 'http://localhost:11435',
                'api_key' => null,
                'status' => 'unknown',
                'capabilities' => ['gemma3:4b'],
                'weight' => 2,
                'priority' => 1,
            ]);

            $this->nodeRepository->create([
                'name' => 'Node-3 (Ollama Port 11436)',
                'driver' => 'ollama',
                'endpoint' => 'http://localhost:11436',
                'api_key' => null,
                'status' => 'unknown',
                'capabilities' => ['mistral:7b'],
                'weight' => 1,
                'priority' => 2,
            ]);
        }
    }
}
