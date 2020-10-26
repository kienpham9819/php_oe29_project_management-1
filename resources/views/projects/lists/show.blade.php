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
                <li class="breadcrumb-item text-capitalize" aria-current="page">
                    <a href="{{ route('projects.task-lists.index', [$project->id]) }}">
                        {{ trans('list.task_list') }}
                    </a>
                </li>
                <li class="breadcrumb-item active text-capitalize" aria-current="page">
                    {{ $taskList->name }}
                </li>
            </ol>
        </nav>
        <div class="row p-3">
            <div class="col-md-8 bg-white border p-3">
                <label class="h3 text-capitalize font-weight-bold">
                    {{ $taskList->name }}
                </label>
                <p>
                    <strong>
                        {{ trans('general.created_at') }} :
                    </strong>
                    {{ $taskList->created_at }}
                    <span class="ml-3 mr-3">
                        |
                    </span>
                    <strong>
                        {{ trans('general.last_updated') }} :
                    </strong>
                    {{ $taskList->updated_at }}
                </p>
                <table class="table">
                    <tr>
                        <th class="w-25">
                            {{ trans('general.description') }} :
                        </th>
                        <td>
                            <p>
                                {{ $taskList->description }}
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <th class="w-25">
                            {{ trans('task.task') }} :
                        </th>
                        <td>
                            {{ count($taskList->tasks) }}
                        </td>
                    </tr>
                    <tr>
                        <th class="w-25">
                            {{ trans('list.assign') }} :
                        </th>
                        <td>
                            {{ $taskList->user->name }}
                        </td>
                    </tr>
                    <tr>
                        <th class="w-25">
                            {{ trans('list.start_date') }} :
                        </th>
                        @if ($taskList->start_date != null)
                            <td>
                                {{ $taskList->start_date->format('Y-m-d') }}
                            </td>
                        @else
                            <td>
                                {{ trans('general.none') }}
                            </td>
                        @endif
                    </tr>
                    <tr>
                        <th class="w-25">
                            {{ trans('list.due_date') }} :
                        </th>
                        <td>
                            {{ $taskList->due_date->format('Y-m-d') }}
                        </td>
                    </tr>
                    <tr>
                        <th class="w-25">
                            {{ trans('general.status') }} :
                        </th>
                        <td>
                            @if ($taskList->start_date == null)
                                <span class="badge badge-secondary">
                                    {{ trans('list.pending') }}
                                </span>
                                @if ($taskList->due_date < date('Y-m-d h:i:sa'))
                                    <span class="badge badge-danger">
                                        <i class="far fa-calendar-times"></i>
                                        {{ date('Y-m-d', strtotime($taskList->due_date)) }}
                                    </span>
                                @endif
                            @elseif ($unfinished != 0)
                                <span class="badge badge-warning">
                                    {{ trans('list.in_progress') }}
                                </span>
                                @if ($taskList->due_date < date('Y-m-d h:i:sa'))
                                    <span class="text-danger">
                                        <i class="far fa-calendar-times"></i>
                                        {{ date('Y-m-d', strtotime($taskList->due_date)) }}
                                    </span>
                                @endif
                            @else
                                <span class="badge badge-success">
                                    {{ trans('list.completed') }}
                                </span>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
            <div class="col-md-4 border bg-white p-3">
                <label class="h3 text-capitalize font-weight-bold">
                    {{ trans('general.stats') }}
                </label>
                <div class="row text-center">
                    <div class="col">
                        <div class="h3">
                            {{ $unfinished }}
                        </div>
                        <div class="text-uppercase">
                            {{ trans('task.unfinished') }}
                        </div>
                    </div>
                    <div class="col">
                        <div class="h3">
                            {{ $completed }}
                        </div>
                        <div class="text-uppercase">
                            {{ trans('task.completed') }}
                        </div>
                    </div>
                </div>
                <table class="table table-hover mt-3">
                    <tbody>
                        <tr>
                            <td>
                                <a class="h5 text-uppercase text-dark" href="{{ route('projects.task-lists.edit', [$project->id, $taskList->id]) }}">
                                    <i class="far fa-edit"></i>
                                    {{ trans('list.edit') }}
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <a class="h5 text-uppercase text-dark" href="#" data-toggle="modal" data-target="#deleteModal">
                                    <i class="far fa-trash-alt"></i>
                                    {{ trans('general.delete') }}
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <calendar locale="{{ session()->get('lang', config('app.locale')) }}"
                                    due_y="{{ date('Y', strtotime($taskList->due_date)) }}"
                                    due_m="{{ date('m', strtotime($taskList->due_date)) }}"
                                    due_d="{{ date('d', strtotime($taskList->due_date)) }}">
                                </calendar>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title text-capitalize" id="deleteLabel">
                                    {{ trans('general.confirm') }}
                                </h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-footer">
                                <form method="POST" action="{{ route('projects.task-lists.destroy', [$project->id, $taskList->id ]) }}">
                                    @csrf
                                    @method('delete')
                                    <button type="submit" class="btn btn-danger">
                                        {{ trans('general.delete') }}
                                    </button>
                                </form>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                    {{ trans('general.close') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="">
            <a class="btn btn-primary text-uppercase w-100" data-toggle="collapse" href="#chart" role="button" aria-expanded="false" aria-controls="chart">
                {{ trans('general.chart') }}
            </a>
            <div class="collapse bg-white p-2 border mt-1" id="chart">
                <div class="container p-2 chart-frame">
                    {{ $chart->container() }}
                    {{ $chart->script() }}
                </div>
                <p class="font-weight-bold mt-2 text-center text-uppercase">
                    {{ trans('list.chart') . ' : ' . $taskList->name }}
                </p>
            </div>
        </div>
        <div class="row p-3">
            <div class="col-md bg-white border p-3">
                <task-input
                    render="{{ route('task-lists.tasks.index', [$taskList->id]) }}"
                    path="{{ route('task-lists.tasks.store', [$taskList->id]) }}"
                    token="{{ csrf_token() }}"
                    task_list_id="{{ $taskList->id }}">
                </task-input>
                <p>
                    @foreach ($errors as $error)
                        <span class="text-danger">
                            {{ $error }}
                        </span>
                    @endforeach
                </p>
                @forelse ($taskList->tasks as $task)
                    <div class="modal fade" id="info{{ $task->id }}" tabindex="-1" role="dialog" aria-labelledby="member" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="member">{{ $task->name }}</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    @forelse ($task->comments as $comment)
                                        <comment id="{{ $comment->id }}"
                                            render="{{ route('comments.show', [$comment->id]) }}"
                                            update_path="{{ route('comments.update', [$comment->id]) }}"
                                            delete_path="{{ route('comments.destroy', [$comment->id]) }}"
                                            user_id="{{ auth()->user()->id }}">
                                        </comment>
                                    @empty
                                        <span class="text-capitalize">
                                            {{ trans('general.empty', ['attribute' => trans('comment.comment')]) }}
                                        </span>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                @endforelse
            </div>
        </div>
    </div>
@endsection
