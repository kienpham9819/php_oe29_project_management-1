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
                <li class="breadcrumb-item active text-capitalize" aria-current="page">
                    {{ $project->name }}
                </li>
            </ol>
        </nav>
        <div class="row p-3">
            <div class="col-md-8 border bg-white p-3">
                <label class="h3 text-capitalize font-weight-bold">
                    {{ $project->name }}
                </label>
                <p>
                    <strong>
                        {{ trans('general.created_at') }} :
                    </strong>
                    {{ $project->created_at }}
                    <span class="ml-3 mr-3">
                        |
                    </span>
                    <strong>
                        {{ trans('general.last_updated') }} :
                    </strong>
                    {{ $project->updated_at }}
                </p>
                <table class="table">
                    <tr>
                        <th class="w-25">
                            {{ trans('general.description') }} :
                        </th>
                        <td>
                            <p>
                                {{ $project->description }}
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <th class="w-25">
                            {{ trans('list.task_list') }} :
                        </th>
                        <td>
                            {{ count($project->taskLists) }}
                        </td>
                    </tr>
                    <tr>
                        <th class="w-25">
                            {{ trans('group.group') }} :
                        </th>
                        <td>
                            {{ $project->group->name }}
                            <a href="#" data-toggle="modal" data-target="#member">
                                ({{ trans('project.view_members') }})
                            </a>
                            <div class="modal fade" id="member" tabindex="-1" role="dialog" aria-labelledby="member" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="member">{{ trans('project.member_list') }}</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <table class="table table-bordered">
                                                <thead class="thead-dark">
                                                    <tr>
                                                        <th scope="col">{{ trans('general.name') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($project->group->users as $key => $user)
                                                        <tr>
                                                            <td>
                                                                {{ $user->name }}
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th class="w-25">
                            {{ trans('course.course') }} :
                        </th>
                        <td>
                            {{ $project->group->course->name }}
                        </td>
                    </tr>
                    <tr>
                        <th class="w-25">
                            {{ trans('general.status') }} :
                        </th>
                        <td>
                            @if ($project->is_accepted)
                                <span class="badge badge-success">
                                    {{ trans('project.approved') }}
                                </span>
                            @else
                                <span class="badge badge-warning">
                                    {{ trans('project.pending') }}
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
                                <a class="h5 text-uppercase text-dark" href="{{ route('projects.task-lists.index', [$project->id]) }}">
                                    <i class="fas fa-list"></i>
                                    {{ trans('list.view') }}
                                </a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        @if ($project->is_completed)
            <div class="text-center text-uppercase border p-3 bg-white">
                <span class="font-weight-bold text-success">
                    <i class="fas fa-check"></i>
                    {{ trans('project.completed') }}
                    <i class="fas fa-check"></i>
                </span>
                <form action="{{ route('updateGrade', $project->id) }}" method="post" class="mt-3 text-left font-weight-bold">
                    @csrf
                    @method('patch')
                    <div class="form-group">
                        <label for="grade">{{ trans('project.grade') }}</label>
                        <input type="number" min="0" max="10" class="form-control" name="grade" id="grade" placeholder="{{ trans('project.grade_project') }}"
                            value="{{ old('grade', $project->grade) }}">
                    </div>
                    <div class="form-group">
                        <label for="review">{{ trans('project.review') }}</label>
                        <textarea name="review" class="form-control" id="review" cols="30" rows="10" placeholder="{{ trans('project.review_project') }}">{{ old('review', $project->review) }}</textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">{{ trans('project.update_grade') }}</button>
                </form>
            </div>
        @endif
    </div>
@endsection
