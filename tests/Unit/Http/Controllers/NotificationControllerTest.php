<?php

namespace Tests\Unit\Http\Controllers;

use Tests\TestCase;
use App\Http\Controllers\NotificationController;
use App\Models\User;
use App\Repositories\User\UserRepositoryInterface;
use Str;
use Mockery;

class NotificationControllerTest extends TestCase
{
    protected $mockUserRepository;

    public function setUp(): void
    {
        parent::setUp();
        $this->mockUserRepository = Mockery::mock(UserRepositoryInterface::class)->makePartial();
        $this->controller = new NotificationController($this->mockUserRepository);
    }

    public function tearDown(): void
    {
        Mockery::close();
        unset($this->controller);
        parent::tearDown();
    }

    public function test_get_notifications()
    {
        $userId = rand();
        $user = new User;
        $user->id = $userId;
        $notifications = [
            (object) [
                'message' => Str::random(100),
                'url' => '/',
                'id' => Str::random(10),
                'created_at' => now(),
            ]
        ];
        $this->mockUserRepository
            ->shouldReceive('getNotifications')
            ->with($userId)
            ->andReturn($notifications)
            ->once();
        $response = $this->actingAs($user)->controller->getAllNotifications();
        $this->assertEquals($notifications, $response);
    }

    public function test_mark_notification_as_read_success()
    {
        $userId = rand();
        $user = new User;
        $user->id = $userId;
        $notificationId = Str::random(10);

        $this->mockUserRepository
            ->shouldReceive('markAsRead')
            ->with($userId, $notificationId)
            ->andReturn(true)
            ->once();
        $response = $this->actingAs($user)->controller->markAsRead($notificationId);
        $this->assertTrue($response);
    }

    public function test_mark_notification_as_read_fail()
    {
        $userId = rand();
        $user = new User;
        $user->id = $userId;
        $notificationId = Str::random(10);

        $this->mockUserRepository
            ->shouldReceive('markAsRead')
            ->with($userId, $notificationId)
            ->andReturn(false)
            ->once();
        $response = $this->actingAs($user)->controller->markAsRead($notificationId);
        $this->assertFalse($response);
    }
}
