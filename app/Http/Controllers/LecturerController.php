<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\GroupRequest;
use App\Http\Requests\AddUserRequest;
use App\Models\Course;
use App\Models\Group;
use App\Models\User;

class LecturerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function listCourse()
    {
        $user = auth()->user();
        $courses = $user->teaches()->paginate(config('paginate.record_number'));
        $newCourses = $user->teaches()
            ->orderBy('updated_at', 'desc')
            ->limit(config('app.display_limit'))->get();

        return view('users.lecturer.course_list', compact([
            'courses',
            'newCourses',
        ]));
    }

    public function showDetailCourse(Course $course)
    {
        $course = Course::with('groups', 'users')->where('courses.id', $course->id)->first();
        $newCourses = auth()->user()->teaches()
            ->orderBy('updated_at', 'desc')
            ->limit(config('app.display_limit'))->get();

        return view('users.lecturer.course_detail', compact([
            'course',
            'newCourses',
        ]));
    }

    public function showFormEditGroup(Group $group)
    {
        $newCourses = auth()->user()->teaches()
            ->orderBy('updated_at', 'desc')
            ->limit(config('app.display_limit'))->get();

        return view('users.lecturer.group_edit', compact([
            'group',
            'newCourses',
        ]));
    }

    public function updateGroup(GroupRequest $request, Group $group)
    {
        $course = Course::findOrFail($group->course_id);
        $groups = $course->groups()->where('name', '!=', $group->name)->get();
        if ($groups->contains('name', $request->name_group)) {
            return redirect()->back()
                ->withErrors(['name_group' => trans('group.unique')])
                ->withInput($request->all());
        } else {
            $group->update([
                'name' => $request->name_group,
            ]);

            return redirect()->route('lecturers.courseDetail', $group->course_id)->with('message', trans('group.edit_noti'));
        }
    }

    public function deleteGroup(Group $group)
    {
        $users = $group->users;
        $project = $group->project;
        if ($project) {
            $project->delete();
        }
        if ($users) {
            $group->users()->detach($users);
        }
        $group->delete();

        return redirect()->back()->with('message', trans('group.delete_noti'));
    }

    public function getUsersHasNoGroup(Group $group)
    {
        $groupIds = Course::findOrFail($group->course_id)->groups()->pluck('groups.id');
        $userIds = Course::findOrFail($group->course_id)->users()->pluck('users.id');
        // danh sách các user ko thuộc 1 group nào trong 1 class cụ thể
        $users = User::whereIn('id', $userIds)
            ->whereNotIn('id', function ($query) use ($groupIds) {
                $query->select('user_id')->from('group_user')
                    ->whereIn('group_id', $groupIds);
            })->get();

        return $users;
    }

    public function groupDetail(Group $group)
    {
        $leader = User::whereIn('id', function ($query) use ($group) {
            $query->select('user_id')->from('group_user')->where('is_leader', config('admin.isLeader'))
                ->where('group_id', $group->id);
        })->first();
        $users = $this->getUsersHasNoGroup($group);
        $newCourses = auth()->user()->teaches()
            ->orderBy('updated_at', 'desc')
            ->limit(config('app.display_limit'))->get();

        return view('users.lecturer.group_detail', compact([
            'group',
            'users',
            'leader',
            'newCourses',
        ]));
    }
}
