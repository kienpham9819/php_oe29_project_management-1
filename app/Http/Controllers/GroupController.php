<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\GroupRequest;
use App\Http\Requests\AddUserRequest;
use App\Models\Course;
use App\Models\Group;
use App\Models\User;
use App\Models\Role;

class GroupController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Course $course, GroupRequest $request)
    {
        $groups = $course->groups;
        if ($groups->contains('name', $request->name_group)) {
            return redirect()->back()
                ->withErrors(['name_group' => trans('group.unique')])
                ->withInput($request->all());
        }
        Group::create([
            'name' => $request->name_group,
            'course_id' => $course->id,
        ]);

        return redirect()->back()->with('message', trans('group.add_noti'));
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

    public function addUserToGroup(AddUserRequest $request, Group $group)
    {
        $users = User::whereIn('id', $request->user_id)->get();
        foreach ($users as $user) {
            $nonGroupedUsers = $this->getUsersHasNoGroup($group);
            if ($user->hasRole('lecturer') || $user->hasRole('admin')) {
                return redirect()->back()->withErrors(['user_id' => trans('course.permission_student')]);
            } elseif (!$nonGroupedUsers->contains($user)) {
                return redirect()->back()->withErrors(['user_id' => trans('course.invalid')]);
            }
        }

        $group->users()->attach($request->user_id);

        return redirect()->back()->with('message', trans('group.noti_addUser'));
    }

    public function addLeaderToGroup(Request $request, Group $group)
    {
        foreach ($group->users as $user) {
            $group->users()->updateExistingPivot($user->id, ['is_leader' => config('admin.isNotLeader')]);
        }
        $group->users()->updateExistingPivot($request->leader, ['is_leader' => config('admin.isLeader')]);
        Role::findOrFail(config('admin.leader'))->users()->syncWithoutDetaching($request->leader);

        return redirect()->back()->with('message', trans('group.noti_addLeader'));
    }

    public function deleteUserFromGroup(Group $group, User $user)
    {
        $group->users()->detach($user);

        return redirect()->back()->with('message', trans('group.noti_deleteUser'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Group $group)
    {
        $newCourses = getLatestCourses();
        $leader = User::whereIn('id', function ($query) use ($group) {
            $query->select('user_id')->from('group_user')->where('is_leader', config('admin.isLeader'))
                ->where('group_id', $group->id);
        })->first();
        $users = $this->getUsersHasNoGroup($group);

        return view('users.admin.group_detail', compact(['group', 'newCourses', 'users', 'leader']));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Group $group)
    {
        $newCourses = getLatestCourses();

        return view('users.admin.group_edit', compact(['group', 'newCourses']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(GroupRequest $request, Group $group)
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

            return redirect()->route('courses.show', $group->course_id)->with('message', trans('group.edit_noti'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Group $group)
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
}
