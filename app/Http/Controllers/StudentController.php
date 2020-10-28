<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Group;
use App\Models\User;
use App\Models\Project;

class StudentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function listCourse()
    {
        $user = auth()->user();
        $courses = $user->courses()->paginate(config('paginate.record_number'));
        $groups = $user->groups()
            ->has('project')
            ->pluck('groups.id');
        $projects = Project::whereIn('group_id', $groups)
            ->with('group.course')
            ->orderBy('updated_at', 'desc')->get();
        $projects->splice(config('app.display_limit'));
        $newCourses = $user->courses()
            ->orderBy('updated_at', 'desc')
            ->limit(config('app.display_limit'))->get();

        return view('users.student.course_list', compact([
            'courses',
            'projects',
            'newCourses',
        ]));
    }

    public function showDetailCourse(Course $course)
    {
        $user = auth()->user();
        $group = $user->groups()->where('course_id', $course->id)->first();
        $groups = $user->groups()
            ->has('project')
            ->pluck('groups.id');
        $projects = Project::whereIn('group_id', $groups)
            ->with('group.course')
            ->orderBy('updated_at', 'desc')->get();
        $projects->splice(config('app.display_limit'));
        $newCourses = $user->courses()
            ->orderBy('updated_at', 'desc')
            ->limit(config('app.display_limit'))->get();

        return view('users.student.course_detail', compact([
            'course',
            'group',
            'projects',
            'newCourses',
        ]));
    }

    public function showDetailGroup(Group $group)
    {
        $leader = User::whereIn('id', function ($query) use ($group) {
            $query->select('user_id')->from('group_user')->where('is_leader', config('admin.isLeader'))
                ->where('group_id', $group->id);
        })->first();
        $user = auth()->user();
        $groups = $user->groups()
            ->has('project')
            ->pluck('groups.id');
        $projects = Project::whereIn('group_id', $groups)
            ->with('group.course')
            ->orderBy('updated_at', 'desc')->get();
        $projects->splice(config('app.display_limit'));
        $newCourses = $user->courses()
            ->orderBy('updated_at', 'desc')
            ->limit(config('app.display_limit'))->get();

        return view('users.student.group_detail', compact([
            'group',
            'leader',
            'projects',
            'newCourses',
        ]));
    }
}
