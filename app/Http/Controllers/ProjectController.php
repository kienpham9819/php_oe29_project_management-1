<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Group;
use App\Models\Project;
use App\Http\Requests\ProjectRequest;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct()
    {
        $this->middleware('auth');
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
        $projects = Project::whereIn('group_id', $groups)->orderBy('updated_at', 'desc')
            ->with(['tasks', 'group.course'])
            ->paginate(config('paginate.record_number'));

        return view('projects.index', compact([
            'courses',
            'projects',
        ]));
    }

    public function store(ProjectRequest $request, Group $group)
    {
        Project::create([
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
    public function show(Project $project)
    {
        $project->load(['group.users', 'taskLists', 'tasks']);
        $unfinished = $project->tasks()->where('is_completed', false)->count();
        $completed = $project->tasks()->where('is_completed', true)->count();
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
    public function edit(Project $project)
    {
        $project->load(['group.users.roles', 'taskLists']);
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
    public function update(ProjectRequest $request, Project $project)
    {
        $project->update([
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
    public function destroy(Project $project)
    {
        $project->delete();
        if (auth()->user()->hasRole('student')) {
            return redirect()->route('projects.index');
        }

        return redirect()->route('groups.show', $project->group->id);
    }

    public function toggle(Project $project)
    {
        $project->is_accepted = !$project->is_accepted;
        $project->save();

        return back();
    }
}
