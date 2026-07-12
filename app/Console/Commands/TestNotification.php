<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Contracts\NotificationServiceInterface;
use App\Contracts\UserRepositoryInterface;
use Illuminate\Console\Command;

class TestNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a test database notification to the first user';

    /**
     * Execute the console command.
     */
    public function handle(UserRepositoryInterface $userRepository, NotificationServiceInterface $notificationService): void
    {
        $user = $userRepository->first();

        if ($user) {
            $notificationService->send(
                $user,
                'Test Notification',
                'If you can see this, database notifications are working perfectly!',
                'heroicon-o-bell',
                'success'
            );

            $this->info("Test notification sent successfully to user: {$user->email}");
        } else {
            $this->error('No users found in the database. Please run migrations and seeders first.');
        }
    }
}
