<?php

namespace App\Repositories\Course;

use App\Repositories\RepositoryInterface;

interface CourseRepositoryInterface extends RepositoryInterface
{
    public function getLatestCourses();

    public function getAllCourses();

    public function restoreCourse($id);

    public function getCourseEagerLoad($id);

    public function getUserIdsInCourse($id);

    public function getGroupIds($group);

    public function getUserIds($group);
}
