<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Models\User;
use Illuminate\Support\Collection;

interface UserRepositoryInterface
{
    /**
     * Get the first user in the database (default administrator).
     */
    public function first(): ?User;

    /**
     * Get all users in the database.
     *
     * @return Collection<User>
     */
    public function all(): Collection;

    /**
     * Get users by their IDs.
     *
     * @param  array<string>  $ids
     * @return Collection<User>
     */
    public function findMany(array $ids): Collection;
}
