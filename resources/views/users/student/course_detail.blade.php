@extends('users.student.index')

@section('breadcrumb')
    <li class="breadcrumb-item text-capitalize" aria-current="page">
        <a href="{{ route('students.courseList') }}">
            {{ trans('general.dashboard') }}
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
                    {{ trans('course.group') }}
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
                                <th scope="col" class="text-center">{{ trans('general.name') }}</th>
                                <th scope="col" class="text-capitalize text-center">
                                    {{ trans('general.details') }}
                                </th>
                            </tr>
                        </thead>
                        <tbody id="table">
                            @if ($group)
                                <tr>
                                    <td class="text-center">
                                        {{ $group->name }}
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('students.groupDetail', $group->id) }}">
                                            <i class="fas fa-info-circle text-info"></i>
                                        </a>
                                    </td>
                                </tr>
                            @else
                                <p>{{ trans('general.empty', ['attribute' => trans('group.group')]) }}</p>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
