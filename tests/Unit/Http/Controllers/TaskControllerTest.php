<?php

namespace Tests\Unit\Http\Controllers;

use Tests\TestCase;
use Mockery;
use App\Models\Task;
use App\Http\Requests\TasksStoreRequest;
use Illuminate\Http\RedirectResponse;
use App\Repositories\Task\TaskRepositoryInterface;
use App\Repositories\TaskList\TaskListRepositoryInterface;
use App\Http\Controllers\TaskController;

class TaskControllerTest extends TestCase
{
    protected $mockTaskRepository, $mockTaskListRepository, $controller;

    public function setUp(): void
    {
        parent::setUp();
        $this->mockTaskRepository = Mockery::mock(TaskRepositoryInterface::class)->makePartial();
        $this->mockTaskListRepository = Mockery::mock(TaskListRepositoryInterface::class)->makePartial();
        $this->controller = new TaskController($this->mockTaskRepository, $this->mockTaskListRepository);
    }

    public function tearDown(): void
    {
        Mockery::close();
        unset($this->controller);
        parent::tearDown();
    }

    public function test_index_method()
    {
        // create fake data
        $id = config('app.default');
        $tasks = factory(Task::class, config('app.display_limit'))->make();
        // mock tasks method from TaskListRepository
        $this->mockTaskListRepository
            ->shouldReceive('tasks')
            ->with($id)
            ->andReturn($tasks)
            ->once();
        $result = $this->controller->index($id);

        $this->assertEquals($tasks, $result);
    }

    public function test_store_method()
    {
        // create fake data
        $id = config('app.default');
        $tasks = [];
        $prev_url = route('projects.task-lists.show', [$id, $id]);
        foreach (factory(Task::class, config('app.display_limit'))->make() as $task)
        {
            $tasks[] = [
                'name' => $task->name,
                'task_list_id' => $task->task_list_id,
                'is_completed' => $task->is_completed,
            ];
        }
        $formRequest = Mockery::mock(TasksStoreRequest::class)->makePartial();
        $formRequest->shouldReceive('all')
            ->andReturn(['tasks' => $tasks]);
        $this->mockTaskRepository
            ->shouldReceive('insert')
            ->with($formRequest->tasks)
            ->andReturn(true)
            ->once();
        $result = $this->from($prev_url)->controller->store($id, $formRequest);
        // check method return back to previous url;
        $this->assertInstanceOf(RedirectResponse::class, $result);
        $this->assertEquals($prev_url, $result->getTargetUrl());
    }

    public function test_destroy_method()
    {
        $id = config('app.default');
        // mock tasks method from TaskRepository
        $this->mockTaskRepository
            ->shouldReceive('delete')
            ->with($id)
            ->andReturn(true)
            ->once();
        $result = $this->controller->destroy($id);

        $this->assertTrue($result);
    }

    public function test_toggle_method()
    {
        $id = config('app.default');
        // mock tasks method from TaskRepository
        $this->mockTaskRepository
            ->shouldReceive('toggle')
            ->with($id)
            ->andReturn(true)
            ->once();
        $result = $this->controller->toggle($id);

        $this->assertTrue($result);
    }
}
