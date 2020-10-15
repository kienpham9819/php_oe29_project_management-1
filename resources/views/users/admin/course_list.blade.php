@extends('users.admin.index')

@section('breadcrumb')
    <li class="breadcrumb-item active text-capitalize" aria-current="page">
       {{ trans('course.title_list') }}
    </li>
@endsection

@section('active-user', 'text-dark')
@section('active-role', 'text-dark')
@section('active-course', 'text-primary')

@section('admin')
    @if (session('message'))
        <div class="alert alert-success noti">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <strong>{{ session('message') }}</strong>
        </div>
    @endif
    <a class="btn btn-primary text-capitalize"
        data-toggle="collapse"
        href="#collapseExample"
        role="button"
        aria-expanded="false"
        aria-controls="collapseExample">
        <i class="fas fa-plus-circle"></i>
        {{ trans('course.add_course_form') }}
    </a>
    <div class="collapse @if ($errors->any()) show @endif" id="collapseExample">
        <div class="card card-body">
            <form action="{{ route('courses.store') }}" method="post" role="form">
                @csrf
                <div class="form-group">
                    <label for="className">{{ trans('general.name') }}</label>
                    @error('className')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                    <input type="text"
                        class="form-control"
                        name="className" id="className"
                        placeholder="{{ trans('course.type_name') }}"
                        value="{{ old('className') }}">
                </div>
                <div class="form-group">
                    <label class="text-capitalize">{{ trans('course.lecturer_id') }}</label>
                    @error('lecturerId')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                    <input class="form-control" id="search_lecturer" type="text" placeholder="{{ trans('course.type_lecturer') }}">
                    <ul class="list-group h-50 overflow-auto" id="listLecturer">
                        @forelse ($lectures as $lecture)
                            <li class="list-group-item">
                                <input type="radio"
                                    value="{{ $lecture->id }}"
                                    name="lecturerId"
                                    id="{{ $lecture->id }}"
                                    {{ old('lecturerId') == $lecture->id ? 'checked' : '' }}>
                                <label for="{{ $lecture->id }}" class="ml-2">{{ $lecture->email }}</label>
                            </li>
                        @empty
                            <p>{{ trans('general.empty', ['attribute' => trans('user.user')]) }}</p>
                        @endforelse
                    </ul>
                </div>

                <button type="submit" class="btn btn-primary">{{ trans('general.save') }}</button>
            </form>
            <hr>
            @if ($errors->any() && !$errors->hasAny(['className', 'lecturerId']))
                @foreach ($errors->all() as $key => $error)
                    <span class="text-danger">{{ $error }}</span></br>
                @endforeach
            @endif
            <form action="{{ route('courses.importCourse') }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="input-group mb-3">
                    <input type="file" name="file" required aria-label="file" accept=".csv,.xlsx,.xls">
                </div>
                <input type="submit" class="btn btn-success" value="{{ trans('course.import') }}">
            </form>
        </div>
    </div>
    <hr>
    <div id="user-list">
        <label class="h3">
            {{ trans('course.list') }}
        </label>
        <table class="table table-hover table-bordered mb-4 mt-3">
            <thead class="thead-dark">
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">{{ trans('general.name') }}</th>
                    <th scope="col" class="text-capitalize">{{ trans('course.lecturer_id') }}</th>
                    <th scope="col" class="text-capitalize">{{ trans('course.lecturer_name') }}</th>
                    <th scope="col" class="text-center">{{ trans('course.edit') }}</th>
                    <th scope="col" class="text-center">{{ trans('general.delete') }}</th>
                    <th scope="col" class="text-center">{{ trans('general.details') }}</th>
                </tr>
            </thead>
            <tbody id="table">
                @forelse ($courses as $key => $course)
                    <tr>
                        <td scope="row"> {{ ++$key }} </td>
                        <td>
                            <label class="font-weight-bold">
                                {{ $course->name }}
                            </label>
                            @isset ($course->deleted_at)
                                <div class="badge badge-danger">
                                    {{ trans('course.deleted') }}
                                </div>
                            @endisset
                        </td>
                        <td>
                            {{ $course->user_id }}
                        </td>
                        <td>
                            {{ $course->user->name }}
                        </td>
                        <td class="text-center">
                            <a href="{{ route('courses.edit', $course->id) }}">
                                <i class="far fa-address-card text-success"></i>
                            </a>
                        </td>
                        <td class="text-center">
                            @isset ($course->deleted_at)
                                <a href="{{ route('courses.restore', $course->id) }}">
                                    <i class="fas fa-trash-restore text-info"></i>
                                </a>
                            @else
                                <a data-toggle="modal" href='#delete{{ $course->id }}'>
                                    <i class="fas fa-user-minus text-danger"></i>
                                </a>
                                <div class="modal fade" id="{{ 'delete' . $course->id }}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title text-capitalize" id="exampleModalLabel">{{ trans('general.confirm') }}</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ trans('general.close') }}</button>
                                                <form action="{{ route('courses.destroy', $course->id) }}"method="post">
                                                    @csrf
                                                    @method('delete')
                                                    <button type="submit" class="btn btn-danger">{{ trans('general.delete') }}</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endisset
                        </td>
                        <td class="text-center">
                            <a href="{{ route('courses.show', $course->id) }}">
                                <i class="fas fa-info-circle text-info"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <p>{{ trans('general.empty', ['attribute' => trans('user.user')]) }}</p>
                @endforelse
            </tbody>
        </table>
        {{ $courses->links() }}
    </div>
@endsection
