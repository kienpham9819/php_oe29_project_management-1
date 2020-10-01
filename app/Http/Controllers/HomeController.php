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
        $groups = $user->groups()->with('project')->orderBy('updated_at', 'desc')->get();

        foreach ($groups as $group) {
            if (isset($group->project)) {
                if ($group->project->is_accepted) {
                    $approved++;
                } else {
                    $pending++;
                }
            }
        }

        $courses = $user->courses()
            ->orderBy('updated_at', 'desc')
            ->limit(config('app.display_limit'))->get();
        $groups->splice(config('app.display_limit'));

        return view('home', compact([
            'groups',
            'courses',
            'pending',
            'approved',
            'unfinished',
            'completed',
        ]));
    }
}
