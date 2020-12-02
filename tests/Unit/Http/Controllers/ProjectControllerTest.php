<?php

namespace Tests\Unit\Http\Controllers;

use Tests\TestCase;
use Mockery;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Repositories\Project\ProjectRepositoryInterface;
use App\Repositories\User\UserRepositoryInterface;
use App\Http\Controllers\ProjectController;
use Illuminate\Http\RedirectResponse;
use App\Jobs\FinishProject;
use App\Jobs\SendWarning;
use App\Models\Project;
use App\Models\User;
use App\Models\Course;
use App\Models\Group;
use App\Http\Requests\GradeRequest;
use Illuminate\Support\Facades\Queue;

class ProjectControllerTest extends TestCase
{
    protected $projectMock, $userMock, $projectController;

    public function setUp() : void
    {
        parent::setUp();
        $this->projectMock = Mockery::mock(ProjectRepositoryInterface::class)->makePartial();
        $this->userMock = Mockery::mock(UserRepositoryInterface::class)->makePartial();
        $this->projectController = new ProjectController($this->projectMock, $this->userMock);
    }

    public function tearDown() : void
    {
        Mockery::close();
        unset($this->projectController);
        parent::tearDown();
    }

    public function test_submit_finish_project_success()
    {
        $projectId = 10;
        $project = factory(Project::class)->make();
        $project->id = $projectId;
        $project->is_completed = 1;
        $this->projectMock->shouldReceive('update')->andReturn($project);
        $lecturer = factory(User::class)->make();
        $this->userMock->shouldReceive('getLecturer')->andReturn($lecturer);
        Queue::fake();
        $response = $this->projectController->submit($projectId);
        Queue::assertPushedWithoutChain(FinishProject::class);
        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals(route('projects.show', [$projectId]), $response->headers->get('Location'));
    }

    public function test_submit_finish_project_fail_with_404_error()
    {
        $projectId = 10;
        $this->projectMock->shouldReceive('update')->andReturn(false);
        $this->expectException(NotFoundHttpException::class);
        $response = $this->projectController->submit($projectId);
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
        $this->projectMock->shouldReceive('getMember')->andReturn($members);
        $project = factory(Project::class)->make();
        $project->id = $projectId;
        $this->projectMock->shouldReceive('update')->andReturn($project);
        $job = Mockery::mock(SendWarning::class);
        Queue::fake();
        $response = $this->projectController->grade($request, $projectId);
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
        $this->projectMock->shouldReceive('update')->andReturn(false);
        $this->expectException(NotFoundHttpException::class);
        $response = $this->projectController->grade($request, $projectId);
    }
}
