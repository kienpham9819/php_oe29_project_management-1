<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Group;
use App\Models\User;
use App\Models\Project;
use App\Repositories\Group\GroupRepositoryInterface;
use App\Repositories\User\UserRepositoryInterface;
use App\Repositories\Course\CourseRepositoryInterface;
use App\Repositories\Project\ProjectRepositoryInterface;

class StudentController extends Controller
{
    protected $groupRepository;
    protected $userRepository;
    protected $courseRepository;
    protected $projectRepository;

    public function __construct(
        GroupRepositoryInterface $groupRepository,
        UserRepositoryInterface $userRepository,
        CourseRepositoryInterface $courseRepository,
        ProjectRepositoryInterface $projectRepository
    ) {
        $this->middleware('auth');
        $this->groupRepository = $groupRepository;
        $this->userRepository = $userRepository;
        $this->courseRepository = $courseRepository;
        $this->projectRepository = $projectRepository;
    }

    public function listCourse()
    {
        $user = auth()->user();
        $courses = $this->courseRepository->getCoursesForStudent($user);
        $groups = $this->groupRepository->getGroupHasProject($user);
        $projects = $this->projectRepository->getLastestProject($groups);
        $newCourses = $this->courseRepository->getLastestCoursesForStudent($user);

        return view('users.student.course_list', compact([
            'courses',
            'projects',
            'newCourses',
        ]));
    }

    public function showDetailCourse($id)
    {
        $course = $this->courseRepository->find($id);
        $this->authorize('view', $course);
        $user = auth()->user();
        $group = $this->groupRepository->getGroupForStudentInCourse($user, $id);
        $groups = $this->groupRepository->getGroupHasProject($user);
        $projects = $this->projectRepository->getLastestProject($groups);
        $newCourses = $this->courseRepository->getLastestCoursesForStudent($user);

        return view('users.student.course_detail', compact([
            'course',
            'group',
            'projects',
            'newCourses',
        ]));
    }

    public function showDetailGroup($id)
    {
        $group = $this->groupRepository->find($id);
        $this->authorize('view', $group);
        $leader = $this->userRepository->getLeader($id);
        $user = auth()->user();
        $groups = $this->groupRepository->getGroupHasProject($user);
        $projects = $this->projectRepository->getLastestProject($groups);
        $newCourses = $this->courseRepository->getLastestCoursesForStudent($user);

        return view('users.student.group_detail', compact([
            'group',
            'leader',
            'projects',
            'newCourses',
        ]));
    }
}
