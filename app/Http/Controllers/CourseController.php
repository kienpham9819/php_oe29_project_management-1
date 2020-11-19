<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\AddUserRequest;
use App\Models\Course;
use App\Models\User;
use App\Models\Role;
use App\Http\Requests\CourseRequest;
use App\Imports\CoursesImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Repositories\Course\CourseRepositoryInterface;
use App\Repositories\User\UserRepositoryInterface;

class CourseController extends Controller
{
    protected $courseRepository;
    protected $userRepository;

    public function __construct(
        CourseRepositoryInterface $courseRepository,
        UserRepositoryInterface $userRepository
    ) {
        $this->middleware('auth');
        $this->courseRepository = $courseRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $newCourses = $this->courseRepository->getLatestCourses();
        $courses = $this->courseRepository->getAllCourses();
        $lectures = $this->userRepository->getLecturers();

        return view('users.admin.course_list', compact([
            'courses',
            'newCourses',
            'lectures',
        ]));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CourseRequest $request)
    {
        $user = $this->userRepository->find($request->lecturerId);
        if ($this->userRepository->checkRoleForUser($user, 'lecturer')) {
            $this->courseRepository->create([
                'name' => $request->className,
                'user_id' => $request->lecturerId,
            ]);

            return redirect()->route('courses.index')
                ->with('message', trans('course.noti_add'));
        }

        return redirect()->back()
            ->withErrors(['lecturer_id' => trans('course.no_permission')])
            ->withInput($request->all());
    }

    public function addUserToCourse(AddUserRequest $request, $id)
    {
        $users = $this->userRepository->getUsersToAddCourse($request->user_id);
        foreach ($users as $user) {
            if ($this->userRepository->checkRoleForUser($user, 'lecturer') || $this->userRepository->checkRoleForUser($user, 'admin')) {
                return redirect()->back()->withErrors(['add_member' => trans('course.permission_student')])->withInput($request->all());
            }
        }
        $course = $this->courseRepository->find($id);
        $this->userRepository->addUserToCourse($course, $request->user_id);

        return redirect()->back()->with('message', trans('course.noti_addUser'));
    }

    public function deleteUserFromCourse($id, $userId)
    {
        $course = $this->courseRepository->find($id);
        $this->userRepository->deleteUserFromCourse($course, $userId);

        return redirect()->back()->with('message', trans('course.noti_deleteUser'));
    }

    public function restoreCourse($id)
    {
        $this->courseRepository->restoreCourse($id);

        return redirect()->route('courses.index')
            ->with('message', trans('course.noti_restore'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $course = $this->courseRepository->getCourseEagerLoad($id);
        $newCourses = $this->courseRepository->getLatestCourses();
        $userIds = $this->courseRepository->getUserIdsInCourse($id);
        //get all user ,that has student role or leader role not exist in this course
        $users = $this->userRepository->getUsersNotInCourse($userIds);

        return view('users.admin.course_detail', compact(['course', 'newCourses', 'users']));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $course = $this->courseRepository->find($id);
        $newCourses = $this->courseRepository->getLatestCourses();
        $lectures = $this->userRepository->getLecturers();

        return view('users.admin.course_edit', compact(['course', 'newCourses', 'lectures']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CourseRequest $request, $id)
    {
        $user = $this->userRepository->find($request->lecturerId);
        if ($this->userRepository->checkRoleForUser($user, 'lecturer')) {
            $this->courseRepository->update($id, [
                'name' => $request->className,
                'user_id' => $request->lecturerId,
            ]);

            return redirect()->route('courses.index')
                ->with('message', trans('course.noti_edit'));
        } else {
            return redirect()->back()->withErrors(['lecturer_id' => trans('course.no_permission')])->withInput($request->all());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->courseRepository->delete($id);

        return redirect()->route('courses.index')
            ->with('message', trans('course.noti_delete'));
    }

    public function importCourse(Request $request)
    {
        Excel::import(new CoursesImport, $request->file('file'));

        return redirect()->route('courses.index')
            ->with('message', trans('course.noti_import'));
    }
}
