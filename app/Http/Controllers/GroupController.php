<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\GroupRequest;
use App\Http\Requests\AddUserRequest;
use App\Models\Course;
use App\Models\Group;
use App\Models\User;
use App\Models\Role;
use App\Repositories\Group\GroupRepositoryInterface;
use App\Repositories\User\UserRepositoryInterface;
use App\Repositories\Course\CourseRepositoryInterface;

class GroupController extends Controller
{
    protected $groupRepository;
    protected $userRepository;
    protected $courseRepository;

    public function __construct(
        GroupRepositoryInterface $groupRepository,
        UserRepositoryInterface $userRepository,
        CourseRepositoryInterface $courseRepository
    ) {
        $this->middleware('auth');
        $this->groupRepository = $groupRepository;
        $this->userRepository = $userRepository;
        $this->courseRepository = $courseRepository;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store($id, GroupRequest $request)
    {
        $data = [
            'name' => $request->name_group,
            'course_id' => $id,
        ];
        $course = $this->courseRepository->find($id);
        $this->authorize('create', $course);
        $groups = $course->groups;
        if ($groups->contains('name', $request->name_group)) {
            return redirect()->back()
                ->withErrors(['name_group' => trans('group.unique')])
                ->withInput($request->all());
        }
        $this->groupRepository->create($data);

        return redirect()->back()->with('message', trans('group.add_noti'));
    }

    public function getUsersHasNoGroup($id)
    {
        $group = $this->groupRepository->find($id);
        $groupIds = $this->courseRepository->getGroupIds($group);
        $userIds = $this->courseRepository->getUserIds($group);
        // danh sách các user ko thuộc 1 group nào trong 1 class cụ thể
        $users = $this->userRepository->getUsersNoGroup($userIds, $groupIds);

        return $users;
    }

    public function addUserToGroup(AddUserRequest $request, $id)
    {
        $userIds = $request->user_id;
        $users = $this->userRepository->getUsersToAddGroup($userIds);
        $group = $this->groupRepository->find($id);
        foreach ($users as $user) {
            $nonGroupedUsers = $this->getUsersHasNoGroup($id);
            if ($this->userRepository->checkRoleForUser($user, 'lecturer') || $this->userRepository->checkRoleForUser($user, 'admin')) {
                if ($this->userRepository->hasRole('admin')) {
                    return redirect()->route('groups.show', $id)->withErrors(['user_id' => trans('course.permission_student')]);
                }

                return redirect()->route('lecturers.groupDetail', $id)->withErrors(['user_id' => trans('course.permission_student')]);
            } elseif (!$nonGroupedUsers->contains($user)) {
                if ($this->userRepository->hasRole('admin')) {
                    return redirect()->route('groups.show', $id)->withErrors(['user_id' => trans('course.invalid')]);
                }

                return redirect()->route('lecturers.groupDetail', $id)->withErrors(['user_id' => trans('course.invalid')]);
            }
        }
        $this->groupRepository->addUsersToGroup($group, $userIds);
        if ($this->userRepository->hasRole('admin')) {
            return redirect()->route('groups.show', $id)->with('message', trans('group.noti_addUser'));
        }

        return redirect()->route('lecturers.groupDetail', $id)->with('message', trans('group.noti_addUser'));
    }

    public function addLeaderToGroup(Request $request, $id)
    {
        $leaderId = $request->leader;
        $group = $this->groupRepository->find($id);
        $this->userRepository->addLeader($group, $leaderId);
        if ($this->userRepository->hasRole('admin')) {
            return redirect()->route('groups.show', $id)->with('message', trans('group.noti_addLeader'));
        }

        return redirect()->route('lecturers.groupDetail', $id)->with('message', trans('group.noti_addLeader'));
    }

    public function deleteUserFromGroup($groupId, $userId)
    {
        $group = $this->groupRepository->find($groupId);
        $user = $this->userRepository->find($userId);
        $this->groupRepository->deleteUser($group, $user);
        if ($this->userRepository->hasRole('admin')) {
            return redirect()->route('groups.show', $groupId)->with('message', trans('group.noti_deleteUser'));
        }

        return redirect()->route('lecturers.groupDetail', $groupId)->with('message', trans('group.noti_deleteUser'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $group = $this->groupRepository->find($id);
        $newCourses = $this->courseRepository->getLatestCourses();
        $users = $this->getUsersHasNoGroup($id);
        $leader = $this->userRepository->getLeader($id);

        return view('users.admin.group_detail', compact(['group', 'newCourses', 'users', 'leader']));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $group = $this->groupRepository->find($id);
        $newCourses = $this->courseRepository->getLatestCourses();

        return view('users.admin.group_edit', compact(['group', 'newCourses']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(GroupRequest $request, $id)
    {
        $group = $this->groupRepository->find($id);
        $course = Course::findOrFail($group->course_id);
        $groups = $course->groups()->where('name', '!=', $group->name)->get();
        if ($groups->contains('name', $request->name_group)) {
            return redirect()->back()
                ->withErrors(['name_group' => trans('group.unique')])
                ->withInput($request->all());
        }
        $this->groupRepository->update($id, ['name' => $request->name_group]);

        return redirect()->route('courses.show', $group->course_id)->with('message', trans('group.edit_noti'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $group = $this->groupRepository->find($id);
        $this->authorize('delete', $group);
        $this->groupRepository->delete($id);

        if ($this->userRepository->hasRole('admin')) {
            return redirect()->route('courses.show', $group->course_id)->with('message', trans('group.delete_noti'));
        }

        return redirect()->route('lecturers.courseDetail', $group->course_id)->with('message', trans('group.delete_noti'));
    }
}
