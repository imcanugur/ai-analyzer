<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\NodeRepositoryInterface;
use App\Contracts\UserRepositoryInterface;
use App\Models\Node;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIClusterService
{
    protected NodeRepositoryInterface $nodeRepository;

    public function __construct(NodeRepositoryInterface $nodeRepository)
    {
        $this->nodeRepository = $nodeRepository;
    }

    /**
     * Check health of a single node and update its state.
     */
    public function checkNodeHealth(Node $node): bool
    {
        $endpoint = rtrim($node->endpoint, '/');

        try {
            if ($node->driver === 'ollama') {
                $url = "{$endpoint}/api/tags";
                $response = Http::timeout(10)->get($url);

                if ($response->successful()) {
                    $data = $response->json();
                    $models = [];

                    if (isset($data['models']) && is_array($data['models'])) {
                        foreach ($data['models'] as $modelData) {
                            if (isset($modelData['name'])) {
                                $models[] = $modelData['name'];
                            }
                        }
                    }

                    $this->nodeRepository->update($node->id, [
                        'status' => 'online',
                        'capabilities' => $models,
                        'last_health_check_at' => now(),
                        'last_error' => null,
                    ]);

                    return true;
                }

                // Fallback to /api/health
                $urlHealth = "{$endpoint}/api/health";
                $responseHealth = Http::timeout(5)->get($urlHealth);

                if ($responseHealth->successful()) {
                    $this->nodeRepository->update($node->id, [
                        'status' => 'online',
                        'last_health_check_at' => now(),
                        'last_error' => null,
                    ]);

                    return true;
                }
            } else {
                $this->nodeRepository->update($node->id, [
                    'status' => 'online',
                    'last_health_check_at' => now(),
                    'last_error' => null,
                ]);

                return true;
            }
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            Log::error("[AI Cluster Service] Node '{$node->name}' check failed: {$errorMessage}");

            $this->nodeRepository->update($node->id, [
                'status' => 'offline',
                'last_health_check_at' => now(),
                'last_error' => 'Connection failed: '.$errorMessage,
            ]);

            // Alert system administrator
            $admin = app(UserRepositoryInterface::class)->first();
            if ($admin) {
                Notification::make()
                    ->title('Cluster Alert: Node Offline')
                    ->body("AI Cluster Node '{$node->name}' ({$node->endpoint}) has gone offline.")
                    ->icon('heroicon-o-exclamation-triangle')
                    ->color('danger')
                    ->danger()
                    ->sendToDatabase($admin);
            }
        }

        return false;
    }
}
