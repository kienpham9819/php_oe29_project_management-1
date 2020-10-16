<?php

namespace App\Http\Controllers;

use App\Models\TaskList;
use App\Models\Project;
use App\Http\Requests\TaskListRequest;
use DB;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TaskListController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Project $project)
    {
        $project->load(['group.users', 'taskLists', 'tasks']);
        $unfinished = $project->tasks()->where('is_completed', false)->count();
        $completed = $project->tasks()->where('is_completed', true)->count();

        return view('projects.lists.index', [
            'project' => $project,
            'completed' => $completed,
            'taskLists' => $project->taskLists->load(['tasks', 'comments'])
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Project $project)
    {
        $project->load('group.users');

        return view('projects.lists.create', compact(['project']));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Project $project, TaskListRequest $request)
    {
        $project->load('group.users');
        if (!$project->group->users->contains($request->user_id)) {
            abort(403);
        }
        $taskList = new TaskList;
        $taskList->name = $request->name;
        $taskList->description = $request->description;
        $taskList->due_date = $request->due_date;
        $taskList->user_id = $request->user_id;
        $taskList->project_id = $project->id;
        $taskList->save();

        return redirect()->route('projects.task-lists.index', [$project->id]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\TaskList  $taskList
     * @return \Illuminate\Http\Response
     */
    public function show(Project $project, TaskList $taskList)
    {
        $taskList->load('tasks.comments', 'tasks.attachments');
        $completed = $taskList->tasks->where('is_completed', 1)->count();
        $unfinished = $taskList->tasks->where('is_completed', 0)->count();

        return view('projects.lists.show', compact([
            'taskList',
            'project',
            'completed',
            'unfinished',
        ]));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\TaskList  $taskList
     * @return \Illuminate\Http\Response
     */
    public function edit(Project $project, TaskList $taskList)
    {
        $taskList->load('tasks.comments', 'tasks.attachments');
        $completed = $taskList->tasks->where('is_completed', 1)->count();
        $unfinished = $taskList->tasks->where('is_completed', 0)->count();
        $project->load('group.users');

        return view('projects.lists.edit', compact([
            'taskList',
            'project',
            'completed',
            'unfinished',
        ]));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\TaskList  $taskList
     * @return \Illuminate\Http\Response
     */
    public function update(TaskListRequest $request, Project $project, TaskList $taskList)
    {
        $project->load(['group.users']);
        if (!$project->group->users->contains($request->user_id)) {
            abort(403);
        }
        $taskList->name = $request->name;
        $taskList->description = $request->description;
        $taskList->due_date = $request->due_date;
        $taskList->start_date = $request->start_date;
        $taskList->user_id = $request->user_id;
        $taskList->save();

        return redirect()->route('projects.task-lists.show', [$project->id, $taskList->id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\TaskList  $taskList
     * @return \Illuminate\Http\Response
     */
    public function destroy(Project $project, TaskList $taskList)
    {
        $taskList->delete();

        return redirect()->route('projects.task-lists.index', [$project->id]);
    }
}
