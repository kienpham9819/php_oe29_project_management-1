<?php

namespace Tests\Unit\Console\Commands;

use Tests\TestCase;
use Mockery;
use App\Models\TaskList;
use App\Models\User;
use App\Models\Project;
use Illuminate\Console\Command;
use App\Repositories\TaskList\TaskListRepositoryInterface;
use App\Repositories\User\UserRepositoryInterface;
use App\Repositories\Project\ProjectRepositoryInterface;
use Carbon\Carbon;
use App\Notifications\Warning;
use App\Console\Commands\WarningDeadline;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Event;
use App\Events\NotifyEvent;

class WarningDeadlineTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */

    protected $taskListMock, $userMock, $projectMock, $warningDeadline;

    public function setUp() : void
    {
        parent::setUp();
        $this->taskListMock = Mockery::mock(TaskListRepositoryInterface::class)->makePartial();
        $this->userMock = Mockery::mock(UserRepositoryInterface::class)->makePartial();
        $this->projectMock = Mockery::mock(ProjectRepositoryInterface::class)->makePartial();
        $this->warningDeadline = new WarningDeadline( $this->taskListMock, $this->userMock, $this->projectMock);
    }

    public function tearDown() : void
    {
        Mockery::close();
        unset($this->warningDeadline);
        parent::tearDown();
    }

    public function test_handle_when_deadline_is_reached()
    {
        $tasklists = factory(TaskList::class, 1)->make();
        $tasklists[0]->id = 10;
        $now = Carbon::now();
        $tasklists[0]->due_date = $now->addDays(config('mail.warning_days'));
        $this->taskListMock->shouldReceive('getAll')->once()->andReturn($tasklists);
        $deadline = $now->subDay(config('mail.warning_days'));
        $user = factory(User::class)->make();
        $this->userMock->shouldReceive('find')->andReturn($user);
        $project = factory(Project::class)->make();
        $project->id = 5;
        $this->projectMock->shouldReceive('find')->andReturn($project);
        $leader = factory(User::class)->make();
        $this->userMock->shouldReceive('getLeader')->andReturn($leader);
        Notification::fake();
        $notifications = array();
        $this->userMock->shouldReceive('getNotifications')->andReturn($notifications);
        Event::fake();
        $this->warningDeadline->handle();
        Notification::assertSentTo(
            $user,
            Warning::class
        );
        Event::assertDispatched(NotifyEvent::class);
    }
}
