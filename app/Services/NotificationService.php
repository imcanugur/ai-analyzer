<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\NotificationRepositoryInterface;
use App\Contracts\NotificationServiceInterface;
use App\Models\DatabaseNotification;
use App\Models\User;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Support\Collection;

class NotificationService implements NotificationServiceInterface
{
    /**
     * Create a new service instance.
     */
    public function __construct(
        protected NotificationRepositoryInterface $repository
    ) {}

    /**
     * Send a database notification to one or more users.
     */
    public function send(User|Collection $users, string $title, string $body, ?string $icon = null, ?string $color = null): void
    {
        $targetUsers = $users instanceof Collection ? $users : collect([$users]);

        foreach ($targetUsers as $user) {
            $notification = FilamentNotification::make()
                ->title($title)
                ->body($body);

            if ($icon) {
                $notification->icon($icon);
            }

            if ($color) {
                $notification->color($color);
            }

            $notification->sendToDatabase($user);
        }
    }

    /**
     * Get all database notifications.
     */
    public function all(): Collection
    {
        return $this->repository->all();
    }

    /**
     * Find a notification by ID.
     */
    public function find(string $id): ?DatabaseNotification
    {
        return $this->repository->find($id);
    }

    /**
     * Delete a notification by ID.
     */
    public function delete(string $id): bool
    {
        return $this->repository->delete($id);
    }

    /**
     * Mark all notifications as read for a user.
     */
    public function markAllAsRead(string $userId): void
    {
        $this->repository->markAllAsRead($userId);
    }

    /**
     * Clear all notifications for a user.
     */
    public function clearAll(string $userId): void
    {
        $this->repository->clearAll($userId);
    }
}
