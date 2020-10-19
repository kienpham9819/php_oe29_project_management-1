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

class CourseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $newCourses = getLatestCourses();
        $courses = Course::orderBy('updated_at', 'desc')
            ->withTrashed()->paginate(config('paginate.record_number'));
        $lectures = Role::findOrFail(config('admin.lecturer'))->users()->get();

        return view('users.admin.course_list', compact(['courses', 'newCourses', 'lectures']));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CourseRequest $request)
    {
        $user = User::findOrFail($request->lecturerId);
        if ($user->hasRole('lecturer')) {
            Course::create([
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

    public function addUserToCourse(AddUserRequest $request, Course $course)
    {
        $users = User::whereIn('id', $request->user_id)->get();
        foreach ($users as $user) {
            if ($user->hasRole('lecturer') || $user->hasRole('admin')) {
                return redirect()->back()->withErrors(['add_member' => trans('course.permission_student')])->withInput($request->all());
            }
        }
        $course->users()->attach($request->user_id);

        return redirect()->back()->with('message', trans('course.noti_addUser'));
    }

    public function deleteUserFromCourse(Request $request, Course $course, User $user)
    {
        $course->users()->detach($user);
        $group = $user->groups->where('course_id', $course->id)->first();
        if ($group) {
            $group->users()->detach($user);
        }

        return redirect()->back()->with('message', trans('course.noti_deleteUser'));
    }

    public function restoreCourse($id)
    {
        Course::withTrashed()->where('id', $id)->restore();

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
        $course = Course::withTrashed()->with('user', 'groups', 'users')->where('courses.id', $id)->first();
        $newCourses = getLatestCourses();
        $userIds = Course::findOrFail($id)->users()->pluck('users.id');
        //get all user ,that has student role or leader role not exist in this course
        $users = User::whereNotIn('id', $userIds)
            ->whereIn('id', function ($query) {
                $query->select('user_id')->from('role_user')
                    ->where('role_id', config('admin.student'))
                    ->orWhere('role_id', config('admin.leader'));
            })->get();

        return view('users.admin.course_detail', compact(['course', 'newCourses', 'users']));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Course $course)
    {
        $newCourses = getLatestCourses();
        $lectures = Role::findOrFail(config('admin.lecturer'))->users()->get();

        return view('users.admin.course_edit', compact(['course', 'newCourses', 'lectures']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CourseRequest $request, Course $course)
    {
        $user = User::findOrFail($request->lecturerId);
        if ($user->hasRole('lecturer')) {
            $course->update([
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
    public function destroy(Course $course)
    {
        $course->delete();

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
