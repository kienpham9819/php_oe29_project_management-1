@extends('layouts.app')

@section('content')
    <div class="container mt-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item text-capitalize" aria-current="page">
                    <a href="{{ route('home') }}">
                        {{ trans('general.dashboard') }}
                    </a>
                </li>
                <li class="breadcrumb-item text-capitalize" aria-current="page">
                    <a href="{{ route('projects.index') }}">
                        {{ trans('project.management') }}
                    </a>
                </li>
                <li class="breadcrumb-item text-capitalize" aria-current="page">
                    <a href="{{ route('projects.show', [$project->id]) }}">
                        {{ $project->name }}
                    </a>
                </li>
                <li class="breadcrumb-item active text-capitalize" aria-current="page">
                    {{ trans('list.task_list') }}
                </li>
            </ol>
        </nav>
        <div class="p-3 border">
            <div class="d-flex justify-content-between mb-3">
                <h3 class="text-uppercase">
                    {{ trans('list.task_list') }}
                </h3>
                @if (auth()->user()->can('create-tasklist') && auth()->user()->can('update', $project))
                    <a class="btn btn-primary text-capitalize" href="{{ route('projects.task-lists.create', [$project->id]) }}">
                        <i class="far fa-list-alt"></i>
                        {{ trans('list.add') }}
                    </a>
                @endif
            </div>
            <hr>
            <div class="row">
                <div class="col overflow-auto">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="font-weight-bold card-title text-center text-capitalize">{{ trans('list.pending') }}</h4>
                        </div>
                        <div class="card-body">
                            @foreach ($taskLists as $taskList)
                                @if ($taskList->start_date == null)
                                    <div class="card mb-3">
                                        <div class="card-body bg-light p-2">
                                            <a class="font-weight-bold" href="{{ route('projects.task-lists.show', [$project->id, $taskList->id]) }}">
                                                {{ $taskList->name }}
                                            </a>
                                            @if ($taskList->due_date < date('Y-m-d h:i:sa'))
                                                <span class="text-danger">
                                                    <i class="far fa-calendar-times"></i>
                                                    {{ date('Y-m-d', strtotime($taskList->due_date)) }}
                                                </span>
                                            @endif
                                            <br>
                                            @if ($taskList->description != null)
                                                <i class="fas fa-align-left text-secondary mr-2" data-toggle="tooltip" title="{{ $taskList->description }}"></i>
                                            @endif
                                            <i class="far fa-comment-alt text-secondary mr-2">
                                                {{ count($taskList->comments) }}
                                            </i>
                                            <span class="badge badge-dark badge-pill">
                                                <i class="far fa-calendar-check"></i>
                                                {{ count($taskList->tasks) }}
                                            </span>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="font-weight-bold card-title text-center text-capitalize">{{ trans('list.in_progress') }}</h4>
                        </div>
                        <div class="card-body">
                            @foreach ($taskLists as $taskList)
                                @if ($taskList->tasks()->where('is_completed', 0)->count() != 0 && $taskList->start_date != null)
                                    <div class="card mb-3">
                                        <div class="card-body bg-light p-2">
                                            <a class="font-weight-bold" href="{{ route('projects.task-lists.show', [$project->id, $taskList->id]) }}">
                                                {{ $taskList->name }}
                                            </a>
                                            <br>
                                            @if ($taskList->description != null)
                                                <i class="fas fa-align-left text-secondary mr-2" data-toggle="tooltip" title="{{ $taskList->description }}"></i>
                                            @endif
                                            <i class="far fa-comment-alt text-secondary mr-2">
                                                {{ count($taskList->comments) }}
                                            </i>
                                            <span class="badge badge-warning badge-pill">
                                                <i class="far fa-calendar-check"></i>
                                                {{ $taskList->tasks->where('is_completed', 1)->count() }} / {{ count($taskList->tasks) }}
                                            </span>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="font-weight-bold card-title text-center text-capitalize">{{ trans('list.completed') }}</h4>
                        </div>
                        <div class="card-body">
                            @foreach ($taskLists as $taskList)
                                @if ($taskList->tasks->where('is_completed', 0)->count() == 0 && $taskList->start_date != null)
                                    <div class="card mb-3">
                                        <div class="card-body bg-light p-2">
                                            <a class="font-weight-bold" href="{{ route('projects.task-lists.show', [$project->id, $taskList->id]) }}">
                                                {{ $taskList->name }}
                                            </a>
                                            <br>
                                            @if ($taskList->description != null)
                                                <i class="fas fa-align-left text-secondary mr-2" data-toggle="tooltip" title="{{ $taskList->description }}"></i>
                                            @endif
                                            <i class="far fa-comment-alt text-secondary mr-2">
                                                {{ count($taskList->comments) }}
                                            </i>
                                            <span class="badge badge-success badge-pill">
                                                <i class="far fa-calendar-check"></i>
                                                {{ count($taskList->tasks) }} / {{ count($taskList->tasks) }}
                                            </span>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
