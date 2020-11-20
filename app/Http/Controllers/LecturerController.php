<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\GroupRequest;
use App\Http\Requests\AddUserRequest;
use App\Models\Course;
use App\Models\Group;
use App\Models\User;
use App\Repositories\Group\GroupRepositoryInterface;
use App\Repositories\User\UserRepositoryInterface;
use App\Repositories\Course\CourseRepositoryInterface;

class LecturerController extends Controller
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

    public function listCourse()
    {
        $user = auth()->user();
        $courses = $this->courseRepository->getCoursesForLecturer($user);
        $newCourses = $this->courseRepository->getLastestCoursesForLecturer($user);

        return view('users.lecturer.course_list', compact([
            'courses',
            'newCourses',
        ]));
    }

    public function showDetailCourse($id)
    {
        $course = $this->courseRepository->find($id);
        $this->authorize('view', $course);
        $course = $this->courseRepository->getCourseEagerLoadForLecturer($id);
        $newCourses = $this->courseRepository->getLastestCoursesForLecturer(auth()->user());

        return view('users.lecturer.course_detail', compact([
            'course',
            'newCourses',
        ]));
    }

    public function showFormEditGroup($id)
    {
        $group = $this->groupRepository->find($id);
        $this->authorize('update', $group);
        $newCourses = $this->courseRepository->getLastestCoursesForLecturer(auth()->user());

        return view('users.lecturer.group_edit', compact([
            'group',
            'newCourses',
        ]));
    }

    public function updateGroup(GroupRequest $request, $id)
    {
        $group = $this->groupRepository->find($id);
        $this->authorize('update', $group);
        $course = $this->courseRepository->find($group->course_id);
        $groups = $course->groups()->where('name', '!=', $group->name)->get();
        if ($groups->contains('name', $request->name_group)) {
            return redirect()->back()
                ->withErrors(['name_group' => trans('group.unique')])
                ->withInput($request->all());
        } else {
            $this->groupRepository->update($id, ['name' => $request->name_group]);

            return redirect()->route('lecturers.courseDetail', $group->course_id)->with('message', trans('group.edit_noti'));
        }
    }

    public function deleteGroup($id)
    {
        $group = $this->groupRepository->find($id);
        $this->groupRepository->delete($id);

        return redirect()->route('lecturers.courseDetail', $group->course_id)->with('message', trans('group.delete_noti'));
    }

    public function getUsersHasNoGroup($id)
    {
        $group = $this->groupRepository->find($id);
        $groupIds = $this->courseRepository->getGroupIds($group);
        $userIds = $this->courseRepository->getUserIds($group);
        $users = $this->userRepository->getUsersNoGroup($userIds, $groupIds);

        return $users;
    }

    public function groupDetail($id)
    {
        $group = $this->groupRepository->find($id);
        $this->authorize('view', $group);
        $users = $this->getUsersHasNoGroup($id);
        $leader = $this->userRepository->getLeader($id);
        $newCourses = $this->courseRepository->getLastestCoursesForLecturer(auth()->user());

        return view('users.lecturer.group_detail', compact([
            'group',
            'users',
            'leader',
            'newCourses',
        ]));
    }
}
