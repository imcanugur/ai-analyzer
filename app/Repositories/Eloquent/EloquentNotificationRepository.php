<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Contracts\NotificationRepositoryInterface;
use App\Models\DatabaseNotification;
use Illuminate\Support\Collection;

class EloquentNotificationRepository implements NotificationRepositoryInterface
{
    /**
     * Get all database notifications.
     */
    public function all(): Collection
    {
        return DatabaseNotification::orderBy('created_at', 'desc')->get();
    }

    /**
     * Find a notification by ID.
     */
    public function find(string $id): ?DatabaseNotification
    {
        return DatabaseNotification::find($id);
    }

    /**
     * Delete a notification by ID.
     */
    public function delete(string $id): bool
    {
        $notification = DatabaseNotification::find($id);
        if ($notification) {
            return (bool) $notification->delete();
        }

        return false;
    }

    /**
     * Mark all notifications as read for a user.
     */
    public function markAllAsRead(string $userId): void
    {
        DatabaseNotification::where('notifiable_id', $userId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    /**
     * Clear all notifications for a user.
     */
    public function clearAll(string $userId): void
    {
        DatabaseNotification::where('notifiable_id', $userId)->delete();
    }
}
