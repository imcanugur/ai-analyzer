<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Models\User;

interface UserRepositoryInterface
{
    /**
     * Get the first user in the database (default administrator).
     */
    public function first(): ?User;

    /**
     * Get all users in the database.
     *
     * @return \Illuminate\Support\Collection<\App\Models\User>
     */
    public function all(): \Illuminate\Support\Collection;

    /**
     * Get users by their IDs.
     *
     * @param array<string> $ids
     * @return \Illuminate\Support\Collection<\App\Models\User>
     */
    public function findMany(array $ids): \Illuminate\Support\Collection;
}
