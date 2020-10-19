<?php

use App\Models\Course;

if (!function_exists('getLatestCourses')) {
    function getLatestCourses()
    {
        $courses = Course::orderBy('updated_at', 'desc')->get();
        $courses->splice(config('app.display_limit'));

        return $courses;
    }
}
