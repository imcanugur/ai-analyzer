<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Contracts\UserRepositoryInterface;
use App\Models\User;
use Illuminate\Support\Collection;

class EloquentUserRepository implements UserRepositoryInterface
{
    /**
     * Get the first user in the database.
     */
    public function first(): ?User
    {
        return User::first();
    }

    /**
     * Get all users in the database.
     */
    public function all(): Collection
    {
        return User::all();
    }

    /**
     * Get users by their IDs.
     */
    public function findMany(array $ids): Collection
    {
        return User::whereIn('id', $ids)->get();
    }
}
