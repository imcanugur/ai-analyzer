<?php

declare(strict_types=1);

namespace App\Actions;

use App\Contracts\NotificationServiceInterface;
use App\Contracts\UserRepositoryInterface;
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
     *
     * @param  array{
     *     send_to_all: bool,
     *     recipients?: array<string>,
     *     title: string,
     *     body: string,
     *     color: string,
     *     icon: string
     * }  $data
     */
    public function execute(array $data): void
    {
        if ($data['send_to_all']) {
            $users = $this->userRepository->all();
        } else {
            $recipients = $data['recipients'] ?? [];
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
            $data['title'],
            $data['body'],
            $data['icon'],
            $data['color']
        );

        FilamentNotification::make()
            ->title('Notifications sent successfully!')
            ->body('Dispatched to ' . $users->count() . ' user(s).')
            ->success()
            ->send();
    }
}
