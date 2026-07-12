<?php

namespace App\Repositories\Eloquent;

use App\Contracts\NodeRepositoryInterface;
use App\Models\Node;
use Illuminate\Support\Collection;

class EloquentNodeRepository implements NodeRepositoryInterface
{
    public function create(array $attributes): Node
    {
        return Node::create($attributes);
    }

    public function find(string $id): ?Node
    {
        return Node::find($id);
    }

    public function update(string $id, array $attributes): bool
    {
        $node = $this->find($id);
        if (! $node) {
            return false;
        }

        return $node->update($attributes);
    }

    public function delete(string $id): bool
    {
        $node = $this->find($id);
        if (! $node) {
            return false;
        }

        return $node->delete();
    }

    public function all(): Collection
    {
        return Node::all();
    }

    public function getActiveNodes(): Collection
    {
        return Node::where('status', 'online')->get();
    }

    public function incrementConnections(string $id): void
    {
        $node = $this->find($id);
        if ($node) {
            $node->increment('active_connections');
        }
    }

    public function decrementConnections(string $id): void
    {
        $node = $this->find($id);
        if ($node) {
            $node->decrement('active_connections');
        }
    }
}
