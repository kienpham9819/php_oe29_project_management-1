<?php

namespace App\Http\Controllers;

use App\Models\Course;
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

    public function index()
    {
        $user = auth()->user();
        $courses = $user->courses()->orderBy('updated_at', 'desc')->limit(config('app.display_limit'))->get();
        $groups = $user->groups()
            ->has('project')
            ->pluck('groups.id');
        $projects = Project::whereIn('id', $groups)->orderBy('updated_at', 'desc')
            ->with(['tasks', 'group.course'])
            ->paginate(config('paginate.record_number'));

        return view('projects.index', compact([
            'courses',
            'projects',
        ]));
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

        return view('projects.show', compact(['project', 'unfinished', 'completed']));
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

        return view('projects.edit', compact('project'));
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

        return redirect()->route('projects.index');
    }
}
