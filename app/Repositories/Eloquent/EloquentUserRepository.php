<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Contracts\UserRepositoryInterface;
use App\Models\User;

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
    public function all(): \Illuminate\Support\Collection
    {
        return User::all();
    }

    /**
     * Get users by their IDs.
     */
    public function findMany(array $ids): \Illuminate\Support\Collection
    {
        return User::whereIn('id', $ids)->get();
    }
}
