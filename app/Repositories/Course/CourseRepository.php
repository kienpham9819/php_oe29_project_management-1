<?php

namespace App\Repositories\Course;

use App\Repositories\Course\CourseRepositoryInterface;
use App\Repositories\BaseRepository;
use App\Models\Course;

class CourseRepository extends BaseRepository implements CourseRepositoryInterface
{
    public function getModel()
    {
        return Course::class;
    }

    public function getLatestCourses()
    {
        $courses = Course::orderBy('updated_at', 'desc')->get();
        $courses->splice(config('app.display_limit'));

        return $courses;
    }

    public function getAllCourses()
    {
        $courses = Course::orderBy('updated_at', 'desc')
            ->withTrashed()->paginate(config('paginate.record_number'));

        return $courses;
    }

    public function restoreCourse($id)
    {
        return Course::withTrashed()->where('id', $id)->restore();
    }

    public function getCourseEagerLoad($id)
    {
        return Course::withTrashed()->with('user', 'groups', 'users')->where('courses.id', $id)->first();
    }

    public function getUserIdsInCourse($id)
    {
        return Course::findOrFail($id)->users()->pluck('users.id');
    }

    public function getGroupIds($group)
    {
        return Course::findOrFail($group->course_id)->groups()->pluck('groups.id');
    }

    public function getUserIds($group)
    {
        return Course::findOrFail($group->course_id)->users()->pluck('users.id');
    }

    public function getCoursesForLecturer($user)
    {
        $courses = $user->teaches()->paginate(config('paginate.record_number'));

        return $courses;
    }

    public function getCoursesForStudent($user)
    {
        $courses = $user->courses()->paginate(config('paginate.record_number'));

        return $courses;
    }

    public function getLastestCoursesForLecturer($user)
    {
        $courses = $user->teaches()
            ->orderBy('updated_at', 'desc')
            ->limit(config('app.display_limit'))->get();

        return $courses;
    }

    public function getLastestCoursesForStudent($user)
    {
        $courses = $user->courses()
            ->orderBy('updated_at', 'desc')
            ->limit(config('app.display_limit'))->get();

        return $courses;
    }

    public function getCourseEagerLoadForLecturer($id)
    {
        $course = Course::with('groups', 'users')->where('courses.id', $id)->first();

        return $course;
    }
}
