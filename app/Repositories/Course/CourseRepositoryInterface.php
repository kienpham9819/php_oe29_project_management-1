<?php

namespace App\Repositories\Course;

use App\Repositories\RepositoryInterface;

interface CourseRepositoryInterface extends RepositoryInterface
{
    public function getLatestCourses();

    public function getAllCourses();

    public function restoreCourse($id);

    public function getCourseEagerLoad($id);

    public function getCourseEagerLoadForLecturer($id);

    public function getUserIdsInCourse($id);

    public function getGroupIds($group);

    public function getUserIds($group);

    public function getCoursesForLecturer($user);

    public function getLastestCoursesForLecturer($user);

    public function getCoursesForStudent($user);

    public function getLastestCoursesForStudent($user);
}
