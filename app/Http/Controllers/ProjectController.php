<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Group;
use App\Models\Project;
use App\Http\Requests\ProjectRequest;
use App\Repositories\Project\ProjectRepositoryInterface;

class ProjectController extends Controller
{
    protected $projectRepository;

    public function __construct(ProjectRepositoryInterface $projectRepository)
    {
        $this->middleware('auth');
        $this->projectRepository = $projectRepository;
    }

    public function create(Group $group)
    {
        $group->load('course');

        return view('projects.create', compact(['group']));
    }

    public function index()
    {
        $user = auth()->user();
        $courses = $user->courses()->orderBy('updated_at', 'desc')->limit(config('app.display_limit'))->get();
        $groups = $user->groups()
            ->has('project')
            ->pluck('groups.id');
        $projects = $this->projectRepository->projectsFromGroups($groups, config('paginate.record_number'));

        return view('projects.index', compact([
            'courses',
            'projects',
        ]));
    }

    public function store(ProjectRequest $request, Group $group)
    {
        $this->projectRepository->create([
            'name' => $request->name,
            'description' => $request->description,
            'group_id' => $group->id,
        ]);

        return redirect()->route('projects.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function show($project)
    {
        $project = $this->projectRepository->find($project, ['group.users', 'taskLists', 'tasks']);
        $this->authorize('view', $project);
        $completed = $this->projectRepository->completedTask($project->id);
        if ($completed === false) {
            abort(404);
        }
        $unfinished = $this->projectRepository->unfinishedTask($project->id);
        if ($unfinished === false) {
            abort(404);
        }
        $user = auth()->user();
        if ($user->hasRole('student')) {
            return view('projects.show', compact(['project', 'unfinished', 'completed']));
        } elseif ($user->hasRole('admin')) {
            return view('users.admin.project_detail', compact(['project', 'unfinished', 'completed']));
        }

        return view('users.lecturer.project_detail', compact(['project', 'unfinished', 'completed']));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function edit($project)
    {
        $project = $this->projectRepository->find($project, ['group.users.roles', 'taskLists']);
        $this->authorize('update', $project);
        if (auth()->user()->hasRole('student')) {
            return view('projects.edit', compact('project'));
        }

        return view('users.admin.project_edit', compact('project'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function update(ProjectRequest $request, $project)
    {
        $project = $this->projectRepository->find($project);
        $this->authorize('update', $project);
        $this->projectRepository->update($project->id, [
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return redirect()->route('projects.show', [$project->id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function destroy($project)
    {
        $project = $this->projectRepository->find($project);
        $this->authorize('delete', $project);
        $this->projectRepository->delete($project->id);
        if (auth()->user()->hasRole('student')) {
            return redirect()->route('projects.index');
        }

        return redirect()->route('groups.show', $project->group->id);
    }

    public function toggle($project)
    {
        if (!$this->projectRepository->toggle($project)){
            abort(404);
        }

        return back();
    }
}
