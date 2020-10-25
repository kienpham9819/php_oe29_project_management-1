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
                    <a href="{{ route('courses.show', [$group->course->id]) }}">
                        {{ $group->course->name }}
                    </a>
                </li>
                <li class="breadcrumb-item text-capitalize" aria-current="page">
                    <a href="{{ route('groups.show', [$group->id]) }}">
                        {{ $group->name }}
                    </a>
                </li>
                <li class="breadcrumb-item active text-capitalize" aria-current="page">
                    {{ trans('project.create') }}
                </li>
            </ol>
        </nav>
        <label class="h3 text-uppercase font-weight-bold">
            {{ trans('project.create') }}
        </label>
        <div class="row p-3">
            <form method="POST" action="{{ route('groups.projects.store', [$group->id]) }}" class="col-md border bg-white p-3">
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
                                name="description">{{ old('description') }}
                            </textarea>
                            @error('description')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </td>
                    </tr>
                    <tr>
                        <th class="w-25">
                            {{ trans('group.group') }} :
                        </th>
                        <td>
                            {{ $group->name }}
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
