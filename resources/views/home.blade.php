@extends('layouts.app')

@section('content')
    <div class="container mt-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item active text-capitalize" aria-current="page">{{ trans('general.dashboard') }}</li>
            </ol>
        </nav>
        <div class="row">
            <div class="col-md-4 mb-2 text-capitalize">
                <ul class="nav flex-column border-left">
                    <li class="nav-item">
                        <a class="nav-link disabled text-primary font-weight-bold h5" href="#">
                            <i class="fas fa-columns"></i>
                            {{ trans('general.dashboard') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-dark font-weight-bold h5">
                            <i class="fas fa-graduation-cap"></i>
                            {{ trans('course.management') }}
                        </a>
                        <ul class="nav flex-column ml-4">
                            @isset($courses)
                                @foreach ($courses as $course)
                                    <li class="nav-item">
                                        <a class="nav-link text-dark" href="#">
                                            <i class="fas fa-caret-right"></i>
                                            {{ $course->name }}
                                        </a>
                                    </li>
                                @endforeach
                                <li class="nav-item">
                                    <a class="nav-link" href="#">
                                        <i class="fas fa-caret-right"></i>
                                        {{ trans('general.more') }} ...
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
                    <li class="nav-item">
                        <a class="nav-link text-dark font-weight-bold h5">
                            <i class="fas fa-project-diagram"></i>
                            {{ trans('project.management') }}
                        </a>
                        <ul class="nav flex-column ml-4">
                            @isset($groups)
                                @foreach ($groups as $group)
                                    @isset($group->project)
                                        <li class="nav-item">
                                            <a class="nav-link text-dark" href="#">
                                                <i class="fas fa-caret-right"></i>
                                                {{ $group->project->name }}
                                            </a>
                                        </li>
                                    @endisset
                                @endforeach
                                <li class="nav-item">
                                    <a class="nav-link" href="#">
                                        <i class="fas fa-caret-right"></i>
                                        {{ trans('general.more') }} ...
                                    </a>
                                </li>
                            @else
                                <li class="nav-item">
                                    <a class="nav-link text-dark" href="#">
                                        <i class="fas fa-caret-right"></i>
                                        {{ trans('general.empty', ['attribute' => trans('project.project')]) }}
                                    </a>
                                </li>
                            @endisset
                        </ul>
                    </li>
                </ul>
            </div>
            <div class="col-md p-3 border bg-white mb-2 mr-3">
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
                    @isset($groups)
                        @foreach ($groups as $group)
                            @isset($group->project)
                                <div class="col-md-4 p-1">
                                    <div class="card">
                                        <div class="card-body">
                                            <h5 class="card-title font-weight-bold">
                                                {{ $group->project->name }}
                                                @if ($group->project->is_accepted)
                                                    <i class="fas fa-check text-success"></i>
                                                @endif
                                            </h5>
                                            <p class="card-text">
                                                {{ $group->project->description }}
                                            </p>
                                        </div>
                                        <ul class="list-group list-group-flush">
                                            <li class="list-group-item">
                                                <strong class="text-capitalize">
                                                    {{ trans('course.course') }} :
                                                </strong>
                                                {{ $group->course->name }}
                                            </li>
                                            <li class="list-group-item">
                                                <strong class="text-capitalize">
                                                    {{ trans('group.group') }} :
                                                </strong>
                                                {{ $group->name }}
                                            </li>
                                        </ul>
                                        <div class="card-body">
                                            <a href="#" class="card-link">
                                                {{ trans('general.details') }}
                                                <i class="fas fa-angle-right"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endisset
                        @endforeach
                    @else
                        <label class="text-capitalize">
                            {{ trans('general.empty', ['attribute' => trans('project.project')]) }}
                        </label>
                    @endisset
                </div>
            </div>
        </div>
    </div>
@endsection
