<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskList;
use Illuminate\Http\Request;
use App\Http\Requests\TasksStoreRequest;

class TaskController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index(TaskList $taskList)
    {
        return $taskList->tasks;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(TaskList $taskList, TasksStoreRequest $request)
    {
        Task::insert($request->tasks);

        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function destroy(Task $task)
    {
        $task->delete();

        return true;
    }

    public function toggle(Task $task)
    {
        $task->is_completed = !$task->is_completed;
        $task->save();

        return true;
    }
}
