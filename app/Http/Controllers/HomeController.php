<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Project;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user = auth()->user();
        $pending = config('app.default');
        $approved = config('app.default');
        $unfinished = $user->tasks()->where('is_completed', false)->count();
        $completed = $user->tasks()->where('is_completed', true)->count();
        $groups = $user->groups()
            ->has('project')
            ->pluck('groups.id');
        $projects = Project::whereIn('group_id', $groups)
            ->with('group.course')
            ->orderBy('updated_at', 'desc')->get();
        $approved = $projects->where('is_accepted', true)->count();
        $pending = count($projects) - $approved;
        $projects->splice(config('app.display_limit'));
        $courses = $user->courses()
            ->orderBy('updated_at', 'desc')
            ->limit(config('app.display_limit'))->get();

        return view('home', compact([
            'courses',
            'projects',
            'pending',
            'approved',
            'unfinished',
            'completed',
        ]));
    }
}
