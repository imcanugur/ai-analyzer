<?php

declare(strict_types=1);

namespace App\Actions;

use App\Contracts\NotificationServiceInterface;
use App\Contracts\UserRepositoryInterface;
use App\DTO\SendNotificationDTO;
use Filament\Notifications\Notification as FilamentNotification;

class SendNotificationAction
{
    /**
     * Create a new action instance.
     */
    public function __construct(
        protected UserRepositoryInterface $userRepository,
        protected NotificationServiceInterface $notificationService
    ) {}

    /**
     * Execute the send notification action.
     */
    public function execute(SendNotificationDTO $dto): void
    {
        if ($dto->sendToAll) {
            $users = $this->userRepository->all();
        } else {
            $recipients = $dto->recipients ?? [];
            $users = $this->userRepository->findMany($recipients);
        }

        if ($users->isEmpty()) {
            FilamentNotification::make()
                ->title('No recipients found')
                ->danger()
                ->send();

            return;
        }

        $this->notificationService->send(
            $users,
            $dto->title,
            $dto->body,
            $dto->icon,
            $dto->color
        );

        FilamentNotification::make()
            ->title('Notifications sent successfully!')
            ->body('Dispatched to ' . $users->count() . ' user(s).')
            ->success()
            ->send();
    }
}
