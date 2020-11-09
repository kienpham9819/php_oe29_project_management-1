@extends('layouts.app')

@section('content')
    <div class="container mt-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item active text-capitalize" aria-current="page">{{ trans('general.dashboard') }}</li>
            </ol>
        </nav>
        <div class="row pr-3">
            <div class="col-md-4 mb-2">
                <ul class="nav flex-column border-left">
                    <li class="nav-item">
                        <a class="nav-link text-primary font-weight-bold h5" href="{{ route('home') }}">
                            <i class="fas fa-columns"></i>
                            {{ trans('general.dashboard') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-dark font-weight-bold h5" href="{{ route('students.courseList') }}">
                            <i class="fas fa-graduation-cap"></i>
                            {{ trans('course.management') }}
                        </a>
                        <ul class="nav flex-column ml-4">
                            @forelse ($courses as $course)
                                <li class="nav-item">
                                    <a class="nav-link text-dark" href="{{ route('students.courseDetail', $course->id) }}">
                                        <i class="fas fa-caret-right"></i>
                                        {{ $course->name }}
                                    </a>
                                </li>
                                    @empty
                                <li class="nav-item">
                                    <a class="nav-link text-dark" href="#">
                                        <i class="fas fa-caret-right"></i>
                                        {{ trans('general.empty', ['attribute' => trans('course.course')]) }}
                                    </a>
                                </li>
                            @endforelse
                            @if (!empty($courses))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('students.courseList') }}">
                                        <i class="fas fa-caret-right"></i>
                                        {{ trans('general.more') }} ...
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-dark font-weight-bold h5" href="{{ route('projects.index') }}">
                            <i class="fas fa-project-diagram"></i>
                            {{ trans('project.management') }}
                        </a>
                        <ul class="nav flex-column ml-4">
                            @forelse ($projects as $project)
                                <li class="nav-item">
                                    <a class="nav-link text-dark"
                                        href="{{ route('projects.show', [$project->id]) }}">
                                        <i class="fas fa-caret-right"></i>
                                        {{ $project->name }}
                                    </a>
                                </li>
                                @empty
                                <li class="nav-item">
                                    <a class="nav-link text-dark" href="#">
                                        <i class="fas fa-caret-right"></i>
                                        {{ trans('general.empty', ['attribute' => trans('project.project')]) }}
                                    </a>
                                </li>
                            @endforelse
                            @if (!empty($projects))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('projects.index') }}">
                                        <i class="fas fa-caret-right"></i>
                                        {{ trans('general.more') }} ...
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </li>
                </ul>
            </div>
            <div class="col-md-8 p-3 border bg-white mb-2">
                <label class="h5 text-uppercase">
                    <i class="fas fa-chart-bar"></i>
                    {{ trans('general.stats') }}
                </label>
                <div class="row p-3">
                    <div class="col-md">
                        <label class="text-uppercase">
                            {{ trans('project.pending') }}
                        </label>
                        <br>
                        <h3>{{ $pending }}</h3>
                    </div>
                    <div class="col-md">
                        <label class="text-uppercase">
                            {{ trans('project.approved') }}
                        </label>
                        <br>
                        <h3>{{ $approved }}</h3>
                    </div>
                    <div class="border-right">
                    </div>
                    <div class="col-md">
                        <label class="text-uppercase">
                            {{ trans('task.unfinished') }}
                        </label>
                        <br>
                        <h3>{{ $unfinished }}</h3>
                    </div>
                    <div class="col-md">
                        <label class="text-uppercase">
                            {{ trans('task.completed') }}
                        </label>
                        <br>
                        <h3>{{ $completed }}</h3>
                    </div>
                </div>
                <hr>
                <label class="h5 text-uppercase">
                    <i class="far fa-clock"></i>
                    {{ trans('project.recent') }}
                </label>
                <div class="row p-3">
                    @forelse ($projects as $project)
                        <div class="col-md-4 p-1">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title font-weight-bold">
                                        <a href="{{ route('projects.show', [$project->id]) }}">
                                            {{ $project->name }}
                                        </a>
                                        @if ($project->is_accepted)
                                            <i class="fas fa-check text-success"></i>
                                        @endif
                                    </h5>
                                    <p class="card-text text-truncate">
                                        {{ $project->description }}
                                    </p>
                                </div>
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item">
                                        <strong class="text-capitalize">
                                            {{ trans('course.course') }} :
                                        </strong>
                                        {{ $project->group->course->name }}
                                    </li>
                                    <li class="list-group-item">
                                        <strong class="text-capitalize">
                                            {{ trans('group.group') }} :
                                        </strong>
                                        {{ $project->name }}
                                    </li>
                                </ul>
                                <div class="card-body">
                                    <a href="{{ route('projects.show', [$project->id]) }}" class="card-link">
                                        {{ trans('general.details') }}
                                        <i class="fas fa-angle-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        @empty
                        <label class="text-capitalize">
                            {{ trans('general.empty', ['attribute' => trans('project.project')]) }}
                        </label>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
