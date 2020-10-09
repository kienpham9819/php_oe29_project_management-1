
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
                <li class="breadcrumb-item active text-capitalize" aria-current="page">
                    {{ trans('project.management') }}
                </li>
            </ol>
        </nav>
        <div class="row">
            <div class="col-md-4 mb-2 text-capitalize">
                <ul class="nav flex-column border-left">
                    <li class="nav-item">
                        <a class="nav-link text-dark font-weight-bold h5" href="{{ route('home') }}">
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
                        <a class="nav-link text-primary font-weight-bold h5" href="{{ route('projects.index') }}">
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
            <div class="col-md p-3 border bg-white mb-2 mr-3">
                <label class="h3 text-uppercase">
                    {{ trans('project.list') }}
                </label>
                <hr>
                <table class="table table-hover table-bordered mb-4">
                    <thead class="thead-dark">
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">{{ trans('general.name') }}</th>
                            <th scope="col">{{ trans('task.task') }}</th>
                            <th scope="col">{{ trans('course.course') }}</th>
                            <th scope="col">{{ trans('general.details') }}</th>
                        </tr>
                    </thead>
                    <tbody id="table">
                        @forelse ($projects as $key => $project)
                            <tr class="text-capitalize">
                                <th scope="row">
                                    {{ ++$key }}
                                </th>
                                <td>
                                    <label class="font-weight-bold h5">
                                        <a href="{{ route('projects.show', [$project->id]) }}">
                                            {{ $project->name }}
                                        </a>
                                    </label>
                                    @if ($project->is_accepted)
                                        <span class="badge badge-success">
                                            {{ trans('project.approved') }}
                                        </span>
                                    @else
                                        <span class="badge badge-warning">
                                            {{ trans('project.pending') }}
                                        </span>
                                    @endif
                                    <br>
                                    {{ trans('general.last_updated') }} :
                                    {{ $project->updated_at }}
                                </td>
                                <td>
                                    {{ $project->tasks()->count() }}
                                </td>
                                <td>
                                    {{ $project->group->course->name }}
                                </td>
                                <td>
                                    <a class="btn btn-primary btn-sm" href="{{ route('projects.show', [$project->id]) }}">
                                        <i class="fas fa-info-circle"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr class="bg-light text-capitalize">
                                <td colspan="5">
                                    <i class="far fa-folder-open"></i>
                                    {{ trans('general.empty', ['attribute' => trans('project.project')]) }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                {{ $projects->links() }}
            </div>
        </div>
    </div>
@endsection
