<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Models\DatabaseNotification;
use App\Models\User;
use Illuminate\Support\Collection;

interface NotificationServiceInterface
{
    /**
     * Send a database notification to one or more users.
     *
     * @param  User|Collection<User>  $users
     */
    public function send(User|Collection $users, string $title, string $body, ?string $icon = null, ?string $color = null): void;

    /**
     * Get all database notifications.
     *
     * @return Collection<DatabaseNotification>
     */
    public function all(): Collection;

    /**
     * Find a notification by ID.
     */
    public function find(string $id): ?DatabaseNotification;

    /**
     * Delete a notification by ID.
     */
    public function delete(string $id): bool;

    /**
     * Mark all notifications as read for a user.
     */
    public function markAllAsRead(string $userId): void;

    /**
     * Clear all notifications for a user.
     */
    public function clearAll(string $userId): void;
}
