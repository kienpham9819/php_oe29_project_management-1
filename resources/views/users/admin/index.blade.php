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
                        <a class="nav-link @yield('active-user') font-weight-bold h5" href="{{ route('users.index') }}">
                            <i class="fas fa-users"></i>
                            {{ trans('user.management') }}
                        </a>
                        <ul class="nav flex-column ml-4">
                            <li class="nav-item">
                                <a class="nav-link text-dark" href="{{ route('users.index') }}">
                                    <i class="fas fa-caret-right"></i>
                                    {{ trans('user.active') }}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-dark" href="{{ route('users.deleted') }}">
                                    <i class="fas fa-caret-right"></i>
                                    {{ trans('user.disable') }}
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link @yield('active-role') font-weight-bold h5" href="{{ route('roles.index') }}">
                            <i class="fas fa-user-tag"></i>
                            {{ trans('role.role') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link @yield('active-course') font-weight-bold h5" href="{{ route('courses.index') }}">
                            <i class="fas fa-school"></i>
                            {{ trans('course.management') }}
                        </a>
                        <ul class="nav flex-column ml-4">
                            @isset ($newCourses)
                                @foreach ($newCourses as $course)
                                    <li class="nav-item">
                                        <a class="nav-link text-dark" href="{{ route('courses.show', $course->id) }}">
                                            <i class="fas fa-caret-right"></i>
                                            {{ $course->name }}
                                        </a>
                                    </li>
                                @endforeach
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('courses.index') }}">
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
                @yield('admin')
            </div>
        </div>
    </div>
@endsection
