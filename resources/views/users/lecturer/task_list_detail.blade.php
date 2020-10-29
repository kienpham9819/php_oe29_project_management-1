@extends('layouts.app')

@section('content')
    <div class="container mt-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item text-capitalize" aria-current="page">
                    <a href="{{ route('lecturers.courseList') }}">
                        {{ trans('course.title_list') }}
                    </a>
                </li>
                <li class="breadcrumb-item text-capitalize" aria-current="page">
                    <a href="{{ route('lecturers.courseDetail', $project->group->course_id) }}">{{ $project->group->course_id }}</a>
                </li>
                <li class="breadcrumb-item text-capitalize" aria-current="page">
                    <a href="{{ route('lecturers.groupDetail', $project->group->id) }}">{{ $project->group->name }}</a>
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
                                <calendar locale="{{ session()->get('lang', config('app.locale')) }}"
                                    due_y="{{ date('Y', strtotime($taskList->due_date)) }}"
                                    due_m="{{ date('m', strtotime($taskList->due_date)) }}"
                                    due_d="{{ date('d', strtotime($taskList->due_date)) }}">
                                </calendar>
                            </td>
                        </tr>
                    </tbody>
                </table>
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
                @forelse ($taskList->tasks as $task)
                    <input type="checkbox"
                    @if ($task->is_completed) checked @endif disabled>
                    {{ $task->name }}
                    <hr>
                @empty
                    <span class="text-capitalize">
                        {{ trans('general.empty', ['attribute' => trans('task.task')]) }}
                    </span>
                @endforelse
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
                                            <label class="font-weight-bold text-primary">
                                                {{ $comment->user->name }}
                                            </label>
                                        </comment>
                                        <hr>
                                    @empty
                                        <span class="text-capitalize">
                                            {{ trans('general.empty', ['attribute' => trans('comment.comment')]) }}
                                        </span>
                                        <hr>
                                    @endforelse
                                    <form method="post" action="{{ route('tasks.comments.store', [$task->id]) }}">
                                        @csrf
                                        <input type="text"
                                            name="content"
                                            class='form-control'
                                            placeholder="{{ trans('comment.enter_comment') }} ...">
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                @endforelse
            </div>
        </div>
        <div class="row p-3">
            <div class="col-md bg-white border p-3">
                <h3 class="text-uppercase font-weight-bold">
                    {{ trans('attachment.attachment') }}
                </h3>
                <hr>
                @forelse ($taskList->tasks as $task)
                    <label class="h5 font-weight-bold">
                        {{ $task->name }}
                    </label>
                    <br>
                    @forelse ($task->attachments as $index => $attachment)
                        <a href="{{ asset('/storage/' . $attachment->url) }}" class="mb-2">
                            {{ ++$index }} . {{ $attachment->name }} <i class="fas fa-file"></i>
                        </a>
                    @empty
                        <span class="text-capitalize">
                            {{ trans('general.empty', ['attribute' => trans('attachment.attachment')]) }}
                        </span>
                    @endforelse
                    <hr>
                @empty
                    <span class="text-capitalize">
                        {{ trans('general.empty', ['attribute' => trans('task.task')]) }}
                    </span>
                @endforelse
            </div>
            @if ($errors->has('urls'))
                <div class="alert alert-danger" role="alert">
                    @foreach ($errors->all() as $message)
                        {{ $message }}
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endsection
