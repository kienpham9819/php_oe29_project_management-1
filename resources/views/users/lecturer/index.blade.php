@extends('layouts.app')

@section('content')
    <div class="container mt-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                @yield('breadcrumb')
            </ol>
        </nav>
        <div class="row">
            <div class="col-md-4 mb-2 text-capitalize">
                <ul class="nav flex-column border-left">
                    <li class="nav-item">
                        <a class="nav-link text-primary font-weight-bold h5">
                            <i class="fas fa-graduation-cap"></i>
                            {{ trans('course.management') }}
                        </a>
                        <ul class="nav flex-column ml-4">
                            @isset ($newCourses)
                                @foreach ($newCourses as $course)
                                    <li class="nav-item">
                                        <a class="nav-link text-dark" href="{{ route('lecturers.courseDetail', $course->id) }}">
                                            <i class="fas fa-caret-right"></i>
                                            {{ $course->name }}
                                        </a>
                                    </li>
                                @endforeach
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('lecturers.courseList') }}">
                                        <i class="fas fa-caret-right"></i>
                                        {{ trans('general.more') }}
                                        ...
                                    </a>
                                </li>
                            @else
                                <li class="nav-item">
                                    <a class="nav-link text-dark" href="#">
                                        <i class="fas fa-caret-right"></i>
                                        {{ trans('general.empty', ['attribute' => trans('course.course')]) }}
                                    </a>
                                </li>
                            @endisset
                        </ul>
                    </li>
                </ul>
            </div>
            <div class="col-md p-3 border bg-white mb-2 mr-3">
                @yield('user')
            </div>
        </div>
    </div>
@endsection
