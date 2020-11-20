<?php

namespace App\Http\Controllers;

use App\Charts\ActivityChart;
use App\Models\Project;
use App\Http\Requests\TaskListRequest;
use DB;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Repositories\TaskList\TaskListRepositoryInterface;
use App\Repositories\Project\ProjectRepositoryInterface;

class TaskListController extends Controller
{
    protected $taskListRepository, $projectRepository;

    public function __construct(TaskListRepositoryInterface $taskListRepository, ProjectRepositoryInterface $projectRepository)
    {
        $this->middleware('auth');
        $this->taskListRepository = $taskListRepository;
        $this->projectRepository = $projectRepository;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($project)
    {
        $project = $this->projectRepository->find($project, ['group.users', 'taskLists', 'tasks']);
        $this->authorize('view', $project);
        $unfinished = $project->tasks()->where('is_completed', false)->count();
        $completed = $project->tasks()->where('is_completed', true)->count();
        $taskLists = $project->taskLists->load(['tasks', 'comments']);
        $user = auth()->user();
        if ($user->hasRole('student')) {
            return view('projects.lists.index', compact([
                'project',
                'completed',
                'taskLists',
            ]));
        } elseif ($user->hasRole('admin')) {
            return view('users.admin.task_list', compact([
                'project',
                'completed',
                'taskLists',
            ]));
        }

        return view('users.lecturer.task_list', compact([
            'project',
            'completed',
            'taskLists',
        ]));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($project)
    {
        $project = $this->projectRepository->find($project, ['group.users']);
        $this->authorize('update', $project);
        if (auth()->user()->hasRole('student')) {
            return view('projects.lists.create', compact(['project']));
        }

        return view('users.admin.task_lists_create', compact(['project']));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store($project, TaskListRequest $request)
    {
        $project = $this->projectRepository->find($project, ['group.users']);
        $this->authorize('update', $project);
        if (!$project->group->users->contains($request->user_id)) {
            abort(403);
        }
        $this->taskListRepository->create([
            'name' => $request->name,
            'description' => $request->description,
            'user_id' => $request->user_id,
            'due_date' => $request->due_date,
            'project_id' => $project->id,
        ]);

        return redirect()->route('projects.task-lists.index', [$project->id]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\TaskList  $taskList
     * @return \Illuminate\Http\Response
     */
    public function show($project, $taskList)
    {
        $project = $this->projectRepository->find($project, ['tasks.comments', 'tasks.attachments']);
        $this->authorize('view', $project);
        $taskList = $this->taskListRepository->find($taskList, ['tasks.comments', 'tasks.attachments']);
        $chart = $this->createActivityChart($taskList->id);
        $completed = $this->taskListRepository->completedTask($taskList->id);
        if ($completed === false) {
            abort(404);
        }
        $unfinished = $this->taskListRepository->unfinishedTask($taskList->id);
        if ($unfinished === false) {
            abort(404);
        }
        $user = auth()->user();
        if ($user->hasRole('student')) {
            return view('projects.lists.show', compact([
                'taskList',
                'project',
                'completed',
                'unfinished',
                'chart',
            ]));
        } elseif ($user->hasRole('lecturer')) {
            return view('users.lecturer.task_list_detail', compact([
                'taskList',
                'project',
                'completed',
                'unfinished',
                'chart',
            ]));
        }

        return view('users.admin.task_list_detail', compact([
            'taskList',
            'project',
            'completed',
            'unfinished',
            'chart',
        ]));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\TaskList  $taskList
     * @return \Illuminate\Http\Response
     */
    public function edit($project, $taskList)
    {
        $project = $this->projectRepository->find($project, ['tasks.comments', 'tasks.attachments']);
        $this->authorize('update', $project);
        $taskList = $this->taskListRepository->find($taskList, ['tasks.comments', 'tasks.attachments']);
        $completed = $this->taskListRepository->completedTask($taskList->id);
        if ($completed === false) {
            abort(404);
        }
        $unfinished = $this->taskListRepository->unfinishedTask($taskList->id);
        if ($unfinished === false) {
            abort(404);
        }
        $project->load('group.users');
        if (auth()->user()->hasRole('student')) {
            return view('projects.lists.edit', compact([
                'taskList',
                'project',
                'completed',
                'unfinished',
            ]));
        }

        return view('users.admin.task_list_edit', compact([
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
    public function update(TaskListRequest $request, $project, $taskList)
    {
        $project = $this->projectRepository->find($project, ['group.users']);
        $this->authorize('update', $project);
        if (!$project->group->users->contains($request->user_id)) {
            abort(403);
        }

        $this->taskListRepository->update($taskList, [
            'name' => $request->name,
            'description' => $request->description,
            'due_date' => $request->due_date,
            'start_date' => $request->start_date,
            'user_id' => $request->user_id,
        ]);

        return redirect()->route('projects.task-lists.show', [$project->id, $taskList]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\TaskList  $taskList
     * @return \Illuminate\Http\Response
     */
    public function destroy($project, $taskList)
    {
        $project = $this->projectRepository->find($project, ['group.users']);
        $this->authorize('delete', $project);
        $this->taskListRepository->delete($taskList);

        return redirect()->route('projects.task-lists.index', [$project->id]);
    }

    private function createActivityChart($id)
    {
        $chart = new ActivityChart;
        $labels = [];
        $data = [];
        $tomorrow = Carbon::tomorrow()->toDateString();
        $date = Carbon::today()->subWeek();

        $tasks = $this->taskListRepository->activities($id);

        do {
            $date = $date->addDay();
            $labels[] = $date->toDateString();
            $hasData = false;
            foreach($tasks as $task) {
                if ($task->date == $date->toDateString()) {
                    $data[] = $task->activities;
                    $hasData = true;
                    break;
                }
            }
            if (!$hasData) {
                $data[] = config('app.default');
            }
        }
        while ($tomorrow != $date->toDateString());
        $chart->labels($labels);
        $chart->dataset(trans('task.completed') , 'line', $data)->options([
            'borderColor' => config('charts.default_color.blue'),
            'backgroundColor' => config('charts.default_color.blue'),
            'fill' => 'true',
            'lineTension' => config('app.default'),
        ]);
        $chart->options([
            'scales' => [
                'yAxes' => [
                    [
                        'ticks' => [
                            'beginAtZero' => true,
                            'stepSize' => 1,
                        ],
                    ],
                ],
            ],
        ]);
        return $chart;
    }
}
