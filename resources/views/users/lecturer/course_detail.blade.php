@extends('users.lecturer.index')

@section('breadcrumb')
    <li class="breadcrumb-item text-capitalize" aria-current="page">
        <a href="{{ route('lecturers.courseList') }}">
            {{ trans('course.title_list') }}
        </a>
    </li>
    <li class="breadcrumb-item active text-capitalize" aria-current="page">
        {{ trans('general.details') . '-' . $course->name }}
    </li>
@endsection

@section('user')
    @if (session('message'))
        <div class="alert alert-success noti">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <strong>{{ session('message') }}</strong>
        </div>
    @endif
    <label>
        <h5><span class="font-weight-bold">{{ trans('course.course') }} :</span> <span>{{ $course->name }}</span></h5>
        <h5><span class="font-weight-bold">{{ trans('course.lecturer') }} :</span> <span>{{ $course->user->name }}</span></h5>
    </label><br>
    <a class="btn btn-success text-capitalize"
        data-toggle="collapse"
        href="#addGroup"
        role="button"
        aria-expanded="false"
        aria-controls="collapseExample">
        <i class="fas fa-users"></i>
        <i class="fas fa-plus"></i>
        {{ trans('course.add_group_form') }}
    </a>
    <div class="collapse mt-2 @if ($errors->has('name_group')) show @endif" id="addGroup">
        <div class="card card-body">
            <form action="{{ route('courses.groups.store', [$course->id]) }}" method="POST" role="form">
                @csrf
                <div class="form-group">
                    <label for="name_group">{{ trans('general.name') }}</label>
                    @error('name_group')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                    <input type="text"
                        class="form-control"
                        name="name_group" id="name_group"
                        placeholder="{{ trans('group.type_name') }}"
                        value="{{ old('name_group') }}">
                </div>
                <button type="submit" class="btn btn-primary">{{ trans('general.save') }}</button>
            </form>
            <hr>
        </div>
    </div>
    <div class="row mt-3">
        <div class="col-3">
            <div class="list-group" id="list-tab" role="tablist">
                <a class="list-group-item list-group-item-action"
                    id="list-home-list"
                    data-toggle="list"
                    href="#list-member"
                    role="tab"
                    aria-controls="home">
                    {{ trans('course.members') }}
                </a>
                <a class="list-group-item list-group-item-action active"
                    id="list-profile-list"
                    data-toggle="list"
                    href="#list-group"
                    role="tab"
                    aria-controls="profile">
                    {{ trans('course.groups') }}
                </a>
            </div>
        </div>
        <div class="col-9">
            <div class="tab-content" id="nav-tabContent">
                <div class="tab-pane fade"
                    id="list-member"
                    role="tabpanel"
                    aria-labelledby="list-home-list">
                    <table class="table table-hover table-bordered mb-4">
                        <thead class="thead-dark">
                            <tr>
                                <th scope="col" class="text-center">#</th>
                                <th scope="col" class="text-center">{{ trans('general.name') }}</th>
                                <th scope="col" class="text-capitalize text-center">{{ trans('user.email') }}</th>
                            </tr>
                        </thead>
                        <tbody id="table">
                            @isset ($course)
                                @forelse ($course->users as $key => $user)
                                    <tr>
                                        <td scope="row" class="text-center"> {{ ++$key }} </td>
                                        <td class="text-center">
                                            {{ $user->name }}
                                        </td>
                                        <td class="text-center">
                                            {{ $user->email }}
                                        </td>
                                    </tr>
                                @empty
                                    <p>{{ trans('general.empty', ['attribute' => trans('user.user')]) }}</p>
                                @endforelse
                            @else
                                <p>{{ trans('general.empty', ['attribute' => trans('user.user')]) }}</p>
                            @endisset
                        </tbody>
                    </table>
                </div>
                <div class="tab-pane fade show active"
                    id="list-group"
                    role="tabpanel"
                    aria-labelledby="list-profile-list">
                    <table class="table table-hover table-bordered mb-4">
                        <thead class="thead-dark">
                            <tr>
                                <th scope="col" class="text-center">#</th>
                                <th scope="col" class="text-center">{{ trans('general.name') }}</th>
                                <th scope="col" class="text-capitalize text-center">
                                    {{ trans('course.edit') }}
                                </th>
                                <th scope="col" class="text-capitalize text-center">
                                    {{ trans('general.delete') }}
                                </th>
                                <th scope="col" class="text-capitalize text-center">
                                    {{ trans('general.details') }}
                                </th>
                            </tr>
                        </thead>
                        <tbody id="table">
                            @forelse ($course->groups as $key => $group)
                                <tr>
                                    <td scope="row" class="text-center"> {{ ++$key }} </td>
                                    <td class="text-center">
                                        {{ $group->name }}
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('lecturers.showFormEditGroup', $group->id) }}">
                                            <i class="fas fa-edit text-primary"></i>
                                        </a>
                                    </td>
                                    <td class="text-center">
                                        <a data-toggle="modal" href='#delete{{ $group->id }}'>
                                            <i class="fas fa-trash-alt text-danger"></i>
                                        </a>
                                        <div class="modal fade" id="{{ 'delete' . $group->id }}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
                                                        <form action="{{ route('groups.destroy', $group->id) }}"method="post">
                                                            @csrf
                                                            @method('delete')
                                                            <button type="submit" class="btn btn-danger">{{ trans('general.delete') }}</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('lecturers.groupDetail', $group->id) }}">
                                            <i class="fas fa-info-circle text-info"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <p>{{ trans('general.empty', ['attribute' => trans('group.group')]) }}</p>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
