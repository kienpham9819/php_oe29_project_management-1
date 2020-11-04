<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\TasksStoreRequest;
use App\Repositories\Task\TaskRepositoryInterface;
use App\Repositories\TaskList\TaskListRepositoryInterface;

class TaskController extends Controller
{
    protected $taskRepository, $taskListRepository;

    public function __construct(
        TaskRepositoryInterface $taskRepository,
        TaskListRepositoryInterface $taskListRepository
    ) {
        $this->middleware('auth');
        $this->taskRepository = $taskRepository;
        $this->taskListRepository = $taskListRepository;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index($id)
    {
        return $this->taskListRepository->tasks($id);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store($id, TasksStoreRequest $request)
    {
        $this->taskRepository->insert($request->tasks);

        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->taskRepository->delete($id);

        return true;
    }

    public function toggle($id)
    {
        $this->taskRepository->toggle($id);

        return true;
    }
}
