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
}
