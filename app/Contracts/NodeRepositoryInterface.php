<?php

namespace App\Contracts;

use App\Models\Node;
use Illuminate\Support\Collection;

interface NodeRepositoryInterface
{
    /**
     * Create a node record.
     */
    public function create(array $attributes): Node;

    /**
     * Find a node record by UUID.
     */
    public function find(string $id): ?Node;

    /**
     * Update a node record.
     */
    public function update(string $id, array $attributes): bool;

    /**
     * Delete a node record by UUID.
     */
    public function delete(string $id): bool;

    /**
     * Get all nodes.
     *
     * @return Collection<int, Node>
     */
    public function all(): Collection;

    /**
     * Get all active (online) nodes.
     *
     * @return Collection<int, Node>
     */
    public function getActiveNodes(): Collection;

    /**
     * Atomically increment the active connections count of a node.
     */
    public function incrementConnections(string $id): void;

    /**
     * Atomically decrement the active connections count of a node.
     */
    public function decrementConnections(string $id): void;
}
