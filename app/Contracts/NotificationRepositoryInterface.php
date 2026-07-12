<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Models\DatabaseNotification;
use Illuminate\Support\Collection;

interface NotificationRepositoryInterface
{
    /**
     * Get all database notifications.
     *
     * @return \Illuminate\Support\Collection<\App\Models\DatabaseNotification>
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
