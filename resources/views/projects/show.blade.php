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
                        @if (auth()->user()->can('update-project') && auth()->user()->can('update', $project))
                            <tr>
                                <td>
                                    <a class="h5 text-uppercase text-dark" href="{{ route('projects.edit', [$project->id]) }}">
                                        <i class="far fa-edit"></i>
                                        {{ trans('project.edit') }}
                                    </a>
                                </td>
                            </tr>
                        @endif
                        <tr>
                            <td>
                                <a class="h5 text-uppercase text-dark" href="{{ route('projects.task-lists.index', [$project->id]) }}">
                                    <i class="fas fa-list"></i>
                                    {{ trans('list.view') }}
                                </a>
                            </td>
                        </tr>
                        @if (auth()->user()->can('delete-project') && auth()->user()->can('delete', $project))
                            <tr>
                                <td>
                                    <a class="h5 text-uppercase text-dark" href="#" data-toggle="modal" data-target="#deleteModal">
                                        <i class="far fa-trash-alt"></i>
                                        {{ trans('general.delete') }}
                                    </a>
                                </td>
                            </tr>
                        @endif
                        @if (auth()->user()->can('update-project') && auth()->user()->can('update', $project))
                            <tr>
                                <td>
                                    <a class="h5 text-uppercase text-dark"
                                        data-toggle="collapse"
                                        href="#gitRepo"
                                        role="button"
                                        aria-expanded="false"
                                        aria-controls="gitRepo">
                                        <i class="fab fa-github-alt"></i>
                                        {{ trans('project.link_github') }}
                                    </a>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
                @if ($project->git_repository)
                    <input class="form-control w-100"
                        type="text"
                        value="{{ $project->git_repository }}"
                        readonly>
                @endif

                @if (auth()->user()->can('update-project') && auth()->user()->can('update', $project))
                    <div class="collapse" id="gitRepo">
                        <div class="card">
                            <ul class="list-group list-group-flush">
                                @foreach ($repositories as $repo)
                                    <li class="list-group-item">
                                        <div class="row">
                                            <div class="col-6">
                                                @if ($repo->private)
                                                    <i class="text-secondary mr-2 fas fa-lock"></i>
                                                @elseif ($repo->fork)
                                                    <i class="text-secondary mr-2 fas fa-code-branch"></i>
                                                @else
                                                    <i class="text-secondary mr-2 fas fa-book"></i>
                                                @endif
                                                <a href="{{ $repo->html_url }}">
                                                    {{ $repo->name }}
                                                </a>
                                            </div>
                                            <div class="col-6">
                                                @if ($repo->html_url != $project->git_repository)
                                                    <form method="post" action="{{ route('projects.link', $project->id) }}">
                                                        @csrf
                                                        @method('patch')
                                                            <input type="hidden" name="git_repository" value="{{ $repo->html_url }}">
                                                            <button class="btn btn-sm btn-secondary" type="submit">
                                                                <i class="fas fa-link"></i>
                                                            </button>
                                                    </form>
                                                @else
                                                    <button class="btn btn-sm btn-success" type="submit">
                                                        <i class="fas fa-link"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif
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
                                <form method="POST" action="{{ route('projects.destroy', [$project->id]) }}">
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
        <div id="submit">
            @if ($project->is_completed)
                <div class="text-center text-uppercase border p-3 bg-white">
                    <span class="font-weight-bold text-success">
                        <i class="fas fa-check"></i>
                        {{ trans('project.completed') }}
                        <i class="fas fa-check"></i>
                    </span>
                    @if ($project->grade)
                        <table class="table text-left mt-3">
                            <tr>
                                <th class="w-25">
                                    {{ trans('project.grade') }} :
                                </th>
                                <td>
                                    {{ $project->grade }}
                                </td>
                            </tr>
                            <tr>
                                <th class="w-25">
                                    {{ trans('project.review') }} :
                                </th>
                                <td>
                                    <p>
                                        {{ $project->review }}
                                    </p>
                                </td>
                            </tr>
                        </table>
                    @endif
                </div>
            @else
                <a class="btn btn-primary w-100 text-uppercase"
                    data-toggle="modal"
                    href="#submit-project"
                    role="button"
                    aria-expanded="false"
                    aria-controls="submit-project">
                    {{ trans('project.submit') }}
                </a>
                <div class="modal fade" id="submit-project" tabindex="-1" role="dialog" aria-labelledby="submitLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title text-capitalize" id="submitLabel">
                                    {{ trans('general.confirm') }}
                                </h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-footer">
                                <form method="post" action="{{ route('projects.submit', $project->id) }}">
                                    @csrf
                                    @method('patch')
                                    <button class="btn btn-primary w-100 text-uppercase">
                                        {{ trans('project.submit') }}
                                    </button>
                                </form>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                    {{ trans('general.close') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
