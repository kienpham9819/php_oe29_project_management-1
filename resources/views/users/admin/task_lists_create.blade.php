@extends('layouts.app')

@section('content')
    <div class="container mt-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item text-capitalize" aria-current="page">
                   <a href="{{ route('courses.index') }}">{{ trans('course.title_list') }}</a>
                </li>
                <li class="breadcrumb-item text-capitalize" aria-current="page">
                    <a href="{{ route('courses.show', $project->group->course_id) }}">{{ $project->group->course_id }}</a>
                </li>
                <li class="breadcrumb-item text-capitalize" aria-current="page">
                    <a href="{{ route('groups.show', $project->group->id) }}">{{ $project->group->name }}</a>
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
                    {{ trans('list.add') }}
                </li>
            </ol>
        </nav>
        <label class="h3 text-uppercase font-weight-bold">
            {{ trans('list.add') }}
        </label>
        <div class="row p-3">
            <form method="POST"
                action="{{ route('projects.task-lists.store', [$project->id]) }}"
                class="col-md border bg-white p-3">
                @csrf
                <div class="form-group row ml-1 mr-1">
                    <input
                        id="name" type="text"
                        class="form-control form-control-lg @error('name') is-invalid @enderror"
                        name="name"
                        placeholder="{{ trans('general.name') }}"
                        autocomplete="name"
                        value="{{ old('name') }}"
                        autofocus>
                    @error('name')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <table class="table">
                    <tr>
                        <th class="w-25">
                            {{ trans('general.description') }} :
                        </th>
                        <td>
                            <textarea
                                id="description" type="text"
                                class="form-control @error('description') is-invalid @enderror"
                                name="description" placeholder="{{ trans('general.description') }}">{{ old('description') }}</textarea>
                            @error('description')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </td>
                    </tr>
                    <tr>
                        <th class="w-25">
                            {{ trans('list.assign') }} :
                        </th>
                        <td>
                            <select class="custom-select" name="user_id">
                                @foreach ($project->group->users as $member)
                                    <option value="{{ $member->id }}">
                                        {{ $member->name }}
                                    </option>
                                @endforeach
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th class="w-25">
                            {{ trans('list.due_date') }} :
                        </th>
                        <td>
                            <input id="due" type="date"
                                name="due_date"
                                class="form-control @error('due_date') is-invalid @enderror"
                                value="{{ old('due_date') }}">
                            @error('due_date')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </td>
                    </tr>
                </table>
                <button class="btn btn-primary text-uppercase w-100">
                    {{ trans('general.save') }}
                </button>
            </form>
        </div>
    </div>
@endsection
