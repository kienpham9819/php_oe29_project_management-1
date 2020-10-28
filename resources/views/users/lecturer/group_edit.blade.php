@extends('users.lecturer.index')

@section('breadcrumb')
    <li class="breadcrumb-item text-capitalize" aria-current="page">
        <a href="{{ route('lecturers.courseList') }}">
            {{ trans('course.title_list') }}
        </a>
    </li>
    <li class="breadcrumb-item text-capitalize" aria-current="page">
        <a href="{{ route('lecturers.courseDetail', $group->course_id) }}">{{ $group->course_id }}</a>
    </li>
    <li class="breadcrumb-item active text-capitalize" aria-current="page">
        {{ trans('group.edit') . '-' . $group->name }}
    </li>
@endsection

@section('user')
    <label class="h3 text-uppercase">
        {{ trans('group.edit_title') }}
    </label>
    <form action="{{ route('lecturers.updateGroup', $group->id) }}" method="POST" role="form">
        @csrf
        @method('patch')
        <div class="form-group">
            <label for="name_group" class="text-capitalize">{{ trans('group.name') }}</label>
            @error('name_group')
                <span class="text-danger">{{ $message }}</span>
            @enderror
            <input type="text"
                class="form-control"
                name="name_group" id="name_group"
                placeholder="{{ trans('group.type_name') }}"
                value="{{ old('name_group', $group->name) }}">
        </div>

        <button type="submit" class="btn btn-primary">{{ trans('general.save') }}</button>
    </form>
@endsection
