<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Group;
use App\Models\User;

class StudentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function listCourse()
    {
        $courses = auth()->user()->courses()->paginate(config('paginate.record_number'));

        return view('users.student.course_list', compact(['courses']));
    }

    public function showDetailCourse(Course $course)
    {
        $group = auth()->user()->groups()->where('course_id', $course->id)->first();

        return view('users.student.course_detail', compact(['course', 'group']));
    }

    public function showDetailGroup(Group $group)
    {
        $leader = User::whereIn('id', function ($query) use ($group) {
            $query->select('user_id')->from('group_user')->where('is_leader', config('admin.isLeader'))
                ->where('group_id', $group->id);
        })->first();

        return view('users.student.group_detail', compact(['group', 'leader']));
    }
}
