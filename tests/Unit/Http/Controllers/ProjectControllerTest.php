<?php

namespace Tests\Unit\Http\Controllers;

use Tests\TestCase;
use App\Events\NotifyEvent;
use App\Http\Controllers\ProjectController;
use App\Http\Requests\GradeRequest;
use App\Http\Requests\ProjectLinkRequest;
use App\Jobs\FinishProject;
use App\Jobs\SendWarning;
use App\Models\Course;
use App\Models\Group;
use App\Models\Project;
use App\Models\User;
use App\Models\Role;
use App\Notifications\ProjectApproved;
use App\Repositories\Project\ProjectRepositoryInterface;
use App\Repositories\User\UserRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Gate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
use Mockery;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProjectControllerTest extends TestCase
{
    protected $mockProjectRepository, $mockUserRepository, $controller;

    public function setUp(): void
    {
        parent::setUp();
        $this->mockProjectRepository = Mockery::mock(ProjectRepositoryInterface::class)->makePartial();
        $this->mockUserRepository = Mockery::mock(UserRepositoryInterface::class)->makePartial();
        $this->controller = new ProjectController($this->mockProjectRepository, $this->mockUserRepository);
        Gate::shouldReceive('authorize')->andReturn(true);
    }

    public function tearDown(): void
    {
        Mockery::close();
        unset($this->controller);
        parent::tearDown();
    }

    public function test_submit_finish_project_success()
    {
        $projectId = 10;
        $project = factory(Project::class)->make();
        $project->id = $projectId;
        $project->is_completed = 1;
        $this->mockProjectRepository->shouldReceive('update')->andReturn($project);
        $lecturer = factory(User::class)->make();
        $this->mockUserRepository->shouldReceive('getLecturer')->andReturn($lecturer);
        Queue::fake();
        $response = $this->controller->submit($projectId);
        Queue::assertPushedWithoutChain(FinishProject::class);
        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals(route('projects.show', [$projectId]), $response->headers->get('Location'));
    }

    public function test_submit_finish_project_fail_with_404_error()
    {
        $projectId = 10;
        $this->mockProjectRepository->shouldReceive('update')->andReturn(false);
        $this->expectException(NotFoundHttpException::class);
        $response = $this->controller->submit($projectId);
    }

    public function test_notificate_grade_success()
    {
        $request = Mockery::mock(GradeRequest::class)->makePartial();
        $request->shouldReceive('all')->andReturn([
            'grade' => 10,
            'review' => 'very good',
        ]);
        $projectId = 10;
        $members = factory(User::class, 5)->make();
        $this->mockProjectRepository->shouldReceive('getMember')->andReturn($members);
        $project = factory(Project::class)->make();
        $project->id = $projectId;
        $this->mockProjectRepository->shouldReceive('update')->andReturn($project);
        $job = Mockery::mock(SendWarning::class);
        Queue::fake();
        $response = $this->controller->grade($request, $projectId);
        Queue::assertPushedWithoutChain(SendWarning::class);
        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals(route('projects.show', [$projectId]), $response->headers->get('Location'));
    }

    public function test_notificate_grade_thrown_404_error()
    {
        $request = Mockery::mock(GradeRequest::class)->makePartial();
        $request->shouldReceive('all')->andReturn([
            'grade' => 10,
            'review' => 'very good',
        ]);
        $projectId = 10;
        $this->mockProjectRepository->shouldReceive('update')->andReturn(false);
        $this->expectException(NotFoundHttpException::class);
        $response = $this->controller->grade($request, $projectId);
    }

    public function test_toggle_project_not_found()
    {
        $id = rand();
        $this->mockProjectRepository
            ->shouldReceive('toggle')
            ->with($id)
            ->andReturn(false)
            ->once();

        $this->expectException(NotFoundHttpException::class);
        $this->controller->toggle($id);
    }

    public function test_toggle_to_pending()
    {
        // generate fake data
        $id = rand();
        $project = factory(Project::class)->make();
        $project->is_accepted = false;
        $project->id = $id;
        $prev_url = route('projects.show', [$id]);

        $this->mockProjectRepository
            ->shouldReceive('toggle')
            ->with($id)
            ->andReturn(true)
            ->once();
        $this->mockProjectRepository
            ->shouldReceive('find')
            ->andReturn($project)
            ->once();
        Notification::fake();
        Event::fake();

        $response = $this->from($prev_url)->controller->toggle($id);
        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals($prev_url, $response->getTargetUrl());
        Notification::assertNothingSent();
        Event::assertNotDispatched(NotifyEvent::class);
    }

    public function test_toggle_to_approved()
    {
        // generate fake data
        $id = rand();
        $project = factory(Project::class)->make();
        $group = factory(Group::class)->make();
        $user = factory(User::class)->make();
        $user->id = $id;
        $group->users = [$user];
        $project->is_accepted = true;
        $project->group = $group;
        $project->id = $id;
        $prev_url = route('projects.show', [$id]);

        $this->mockProjectRepository
            ->shouldReceive('toggle')
            ->with($id)
            ->andReturn(true)
            ->once();
        $this->mockProjectRepository
            ->shouldReceive('find')
            ->andReturn($project)
            ->once();
        $this->mockUserRepository
            ->shouldReceive('getNotifications')
            ->andReturn([])
            ->once();
        Notification::fake();
        Event::fake();

        $response = $this->from($prev_url)->controller->toggle($id);
        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals($prev_url, $response->getTargetUrl());
        Event::assertDispatched(NotifyEvent::class, 1);
        Notification::assertSentTo(
            $user,
            ProjectApproved::class
        );
    }

    public function test_link_project_to_github()
    {
        // generate fake data
        $id = rand();
        $user = factory(User::class)->make();
        $user->id = $id;
        $group = factory(Group::class)->make();
        $user->groups = [$group];
        $group->id = $id;
        $group->pivot = (object) [
            'is_leader' => true,
        ];
        $project = factory(Project::class)->make();
        $project->id = $id;
        $project->group = $group;

        $formRequest = Mockery::mock(ProjectLinkRequest::class)->makePartial();
        $formRequest->shouldReceive('all')
            ->andReturn(['git_repository' => '/']);
        $this->mockProjectRepository
            ->shouldReceive('find')
            ->with($id)
            ->andReturn($project)
            ->once();
        $this->mockProjectRepository
            ->shouldReceive('update')
            ->with($id, [
                'git_repository' => '/',
            ])
            ->andReturn($project)
            ->once();

        $response = $this
            ->actingAs($user)
            ->controller
            ->linkGithubRepository($formRequest, $id);
        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals(route('projects.show', [$id]), $response->getTargetUrl());
    }

    public function test_show_project_when_get_completed_task_fail()
    {
        // generate fake data
        $id = rand();
        $user = factory(User::class)->make();
        $user->id = $id;
        $group = factory(Group::class)->make();
        $course = factory(Course::class)->make();
        $course->user_id = $id;
        $user->groups = [$group];
        $group->id = $id;
        $group->course = $course;
        $project = factory(Project::class)->make();
        $project->id = $id;
        $project->group = $group;

        $this->mockProjectRepository
            ->shouldReceive('find')
            ->with($id, ['group.users', 'taskLists', 'tasks'])
            ->andReturn($project)
            ->once();
        $this->mockProjectRepository
            ->shouldReceive('completedTask')
            ->with($id)
            ->andReturn(false)
            ->once();

        $this->expectException(NotFoundHttpException::class);
        $response = $this
            ->actingAs($user)
            ->controller
            ->show($id, new Request);
    }

    public function test_show_project_when_get_unfinished_task_fail()
    {
        $id = rand();
        $user = factory(User::class)->make();
        $user->id = $id;
        $group = factory(Group::class)->make();
        $course = factory(Course::class)->make();
        $course->user_id = $id;
        $user->groups = [$group];
        $group->id = $id;
        $group->course = $course;
        $project = factory(Project::class)->make();
        $project->id = $id;
        $project->group = $group;

        $this->mockProjectRepository
            ->shouldReceive('find')
            ->with($id, ['group.users', 'taskLists', 'tasks'])
            ->andReturn($project)
            ->once();
        $this->mockProjectRepository
            ->shouldReceive('completedTask')
            ->with($id)
            ->andReturn(true)
            ->once();
        $this->mockProjectRepository
            ->shouldReceive('unfinishedTask')
            ->with($id)
            ->andReturn(false)
            ->once();

        $this->expectException(NotFoundHttpException::class);
        $response = $this
            ->actingAs($user)
            ->controller
            ->show($id, new Request);
    }

    public function test_show_project_for_student()
    {
        $id = rand();
        $user = factory(User::class)->make();
        $user->id = $id;
        $group = factory(Group::class)->make();
        $course = factory(Course::class)->make();
        $course->user_id = $id;
        $user->groups = [$group];
        $group->id = $id;
        $group->course = $course;
        $project = factory(Project::class)->make();
        $project->id = $id;
        $project->group = $group;
        $request =  new Request;

        $this->mockProjectRepository
            ->shouldReceive('find')
            ->with($id, ['group.users', 'taskLists', 'tasks'])
            ->andReturn($project)
            ->once();
        $this->mockProjectRepository
            ->shouldReceive('completedTask')
            ->with($id)
            ->andReturn(true)
            ->once();
        $this->mockProjectRepository
            ->shouldReceive('unfinishedTask')
            ->with($id)
            ->andReturn(true)
            ->once();
        $this->mockUserRepository
            ->shouldReceive('hasRole')
            ->andReturn(true);
        $this->mockUserRepository
            ->shouldReceive('getGithubRepositories')
            ->with($id, $request->header('User-Agent'))
            ->andReturn([])
            ->once();

        $response = $this
            ->actingAs($user)
            ->controller
            ->show($id, $request);
        $this->assertEquals('projects.show', $response->getName());
    }

    public function test_show_project_for_admin()
    {
        $id = rand();
        $user = factory(User::class)->make();
        $user->id = $id;
        $group = factory(Group::class)->make();
        $course = factory(Course::class)->make();
        $course->user_id = $id;
        $user->groups = [$group];
        $group->id = $id;
        $group->course = $course;
        $project = factory(Project::class)->make();
        $project->id = $id;
        $project->group = $group;
        $request =  new Request;

        $this->mockProjectRepository
            ->shouldReceive('find')
            ->with($id, ['group.users', 'taskLists', 'tasks'])
            ->andReturn($project)
            ->once();
        $this->mockProjectRepository
            ->shouldReceive('completedTask')
            ->with($id)
            ->andReturn(true)
            ->once();
        $this->mockProjectRepository
            ->shouldReceive('unfinishedTask')
            ->with($id)
            ->andReturn(true)
            ->once();
        $this->mockUserRepository
            ->shouldReceive('hasRole')
            ->andReturn(false)
            ->once();
        $this->mockUserRepository
            ->shouldReceive('hasRole')
            ->andReturn(true)
            ->once();

        $response = $this
            ->actingAs($user)
            ->controller
            ->show($id, $request);
        $this->assertEquals('users.admin.project_detail', $response->getName());
    }

    public function test_show_project_for_lecturer()
    {
        $id = rand();
        $user = factory(User::class)->make();
        $user->id = $id;
        $group = factory(Group::class)->make();
        $course = factory(Course::class)->make();
        $course->user_id = $id;
        $user->groups = [$group];
        $group->id = $id;
        $group->course = $course;
        $project = factory(Project::class)->make();
        $project->id = $id;
        $project->group = $group;
        $request =  new Request;

        $this->mockProjectRepository
            ->shouldReceive('find')
            ->with($id, ['group.users', 'taskLists', 'tasks'])
            ->andReturn($project)
            ->once();
        $this->mockProjectRepository
            ->shouldReceive('completedTask')
            ->with($id)
            ->andReturn(true)
            ->once();
        $this->mockProjectRepository
            ->shouldReceive('unfinishedTask')
            ->with($id)
            ->andReturn(true)
            ->once();
        $this->mockUserRepository
            ->shouldReceive('hasRole')
            ->andReturn(false);

        $response = $this
            ->actingAs($user)
            ->controller
            ->show($id, $request);
        $this->assertEquals('users.lecturer.project_detail', $response->getName());
    }
}
