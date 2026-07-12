<?php

namespace Tests\Feature;

use App\Contracts\NotificationRepositoryInterface;
use App\Models\DatabaseNotification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationSoftDeleteTest extends TestCase
{
    use RefreshDatabase;

    private NotificationRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(NotificationRepositoryInterface::class);
    }

    public function test_notification_can_be_soft_deleted(): void
    {
        // 1. Create a user
        $user = User::factory()->create();

        // 2. Create a notification
        $notification = DatabaseNotification::create([
            'id' => \Illuminate\Support\Str::uuid()->toString(),
            'type' => 'App\\Notifications\\TestNotification',
            'notifiable_type' => User::class,
            'notifiable_id' => $user->id,
            'data' => ['message' => 'Hello World'],
        ]);

        // Verify notification is created
        $this->assertDatabaseHas('notifications', [
            'id' => $notification->id,
            'deleted_at' => null,
        ]);

        // 3. Delete notification using repository
        $deleted = $this->repository->delete($notification->id);
        $this->assertTrue($deleted);

        // 4. Assert soft delete behavior:
        // - Record should still be in the database, but with deleted_at set
        $this->assertDatabaseHas('notifications', [
            'id' => $notification->id,
        ]);
        $this->assertNotNull(
            DatabaseNotification::withTrashed()->find($notification->id)->deleted_at
        );

        // - It should not be returned by standard query (all or find)
        $this->assertNull(DatabaseNotification::find($notification->id));
        $this->assertNull($this->repository->find($notification->id));
        $this->assertFalse($this->repository->all()->contains('id', $notification->id));
    }

    public function test_clear_all_soft_deletes_notifications(): void
    {
        $user = User::factory()->create();

        $notification1 = DatabaseNotification::create([
            'id' => \Illuminate\Support\Str::uuid()->toString(),
            'type' => 'App\\Notifications\\TestNotification',
            'notifiable_type' => User::class,
            'notifiable_id' => $user->id,
            'data' => ['message' => 'Message 1'],
        ]);

        $notification2 = DatabaseNotification::create([
            'id' => \Illuminate\Support\Str::uuid()->toString(),
            'type' => 'App\\Notifications\\TestNotification',
            'notifiable_type' => User::class,
            'notifiable_id' => $user->id,
            'data' => ['message' => 'Message 2'],
        ]);

        // Clear all notifications for user
        $this->repository->clearAll($user->id);

        // Assert they are soft deleted
        $this->assertNotNull(DatabaseNotification::withTrashed()->find($notification1->id)->deleted_at);
        $this->assertNotNull(DatabaseNotification::withTrashed()->find($notification2->id)->deleted_at);

        $this->assertNull(DatabaseNotification::find($notification1->id));
        $this->assertNull(DatabaseNotification::find($notification2->id));
    }

    public function test_notification_service_sends_notifications(): void
    {
        $user = User::factory()->create();
        $service = app(\App\Contracts\NotificationServiceInterface::class);

        $service->send(
            $user,
            'Service Title',
            'Service Body',
            'heroicon-o-megaphone',
            'info'
        );

        $notification = DatabaseNotification::first();

        $this->assertNotNull($notification);
        $this->assertEquals($user->id, $notification->notifiable_id);
        $this->assertEquals('Service Title', $notification->data['title']);
        $this->assertEquals('Service Body', $notification->data['body']);
        $this->assertEquals('heroicon-o-megaphone', $notification->data['icon']);
        $this->assertEquals('info', $notification->data['color']);
    }

    public function test_send_notification_action_sends_to_all_users(): void
    {
        $users = User::factory()->count(3)->create();
        $action = app(\App\Actions\SendNotificationAction::class);

        $action->execute([
            'send_to_all' => true,
            'title' => 'Global Title',
            'body' => 'Global Body',
            'color' => 'success',
            'icon' => 'heroicon-o-bell',
        ]);

        $this->assertEquals(3, DatabaseNotification::count());
        foreach ($users as $user) {
            $this->assertEquals(1, DatabaseNotification::where('notifiable_id', $user->id)->count());
        }
    }

    public function test_send_notification_action_sends_to_selected_recipients(): void
    {
        $users = User::factory()->count(3)->create();
        $action = app(\App\Actions\SendNotificationAction::class);

        $action->execute([
            'send_to_all' => false,
            'recipients' => [$users[0]->id, $users[1]->id],
            'title' => 'Selected Title',
            'body' => 'Selected Body',
            'color' => 'warning',
            'icon' => 'heroicon-o-bell',
        ]);

        $this->assertEquals(2, DatabaseNotification::count());
        $this->assertEquals(1, DatabaseNotification::where('notifiable_id', $users[0]->id)->count());
        $this->assertEquals(1, DatabaseNotification::where('notifiable_id', $users[1]->id)->count());
        $this->assertEquals(0, DatabaseNotification::where('notifiable_id', $users[2]->id)->count());
    }

    public function test_user_notifications_relationship_uses_custom_model_and_soft_deletes(): void
    {
        $user = User::factory()->create();

        $notification = DatabaseNotification::create([
            'id' => \Illuminate\Support\Str::uuid()->toString(),
            'type' => 'App\\Notifications\\TestNotification',
            'notifiable_type' => User::class,
            'notifiable_id' => $user->id,
            'data' => ['message' => 'Relationship Test'],
        ]);

        // Get notification through relation
        $retrieved = $user->notifications()->find($notification->id);
        $this->assertInstanceOf(DatabaseNotification::class, $retrieved);

        // Delete via relationship
        $user->notifications()->where('id', $notification->id)->delete();

        // Assert it is soft-deleted
        $this->assertNotNull(DatabaseNotification::withTrashed()->find($notification->id)->deleted_at);
        $this->assertNull($user->notifications()->find($notification->id));
    }

    public function test_filament_clear_notifications_action_soft_deletes(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Create a notification in the format Filament expects
        $notification = DatabaseNotification::create([
            'id' => \Illuminate\Support\Str::uuid()->toString(),
            'type' => 'App\\Notifications\\TestNotification',
            'notifiable_type' => User::class,
            'notifiable_id' => $user->id,
            'data' => [
                'format' => 'filament',
                'title' => 'Test',
                'body' => 'Test body'
            ],
        ]);

        // Test Livewire component clearNotifications
        \Livewire\Livewire::test(\Filament\Notifications\Livewire\DatabaseNotifications::class)
            ->call('clearNotifications');

        // Assert it is soft-deleted
        $this->assertNotNull(DatabaseNotification::withTrashed()->find($notification->id)->deleted_at);
        $this->assertNull(DatabaseNotification::find($notification->id));
    }

    public function test_sql_query_for_delete(): void
    {
        $user = User::factory()->create();
        $query = $user->notifications()->where('data->format', 'filament');
        fwrite(STDERR, "\n[DEBUG] SQL: " . $query->toSql() . "\n");
        
        // If soft deletes are active, the query will automatically scope out deleted records
        $this->assertStringContainsString('deleted_at', $query->toSql());
    }
}
