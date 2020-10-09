@extends('users.student.index')

@section('breadcrumb')
    <li class="breadcrumb-item text-capitalize" aria-current="page">
        <a href="{{ route('students.courseList') }}">
            {{ trans('general.dashboard') }}
        </a>
    </li>
    <li class="breadcrumb-item text-capitalize" aria-current="page">
        <a href="{{ route('students.courseDetail', $group->course_id) }}">{{ $group->course_id }}</a>
    </li>
    <li class="breadcrumb-item active text-capitalize" aria-current="page">
        {{ trans('group.detail') . '-' . $group->name }}
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
        <h5><span class="font-weight-bold">{{ trans('group.group') }} :</span> <span>{{ $group->name }}</span></h5>
        <h5><span class="font-weight-bold">{{ trans('group.leader') }} :</span>
        <span>
            {{ $leader->name ?? '#' }}
        </span></h5>
    </label><br>

    <div id="user-list">
        <label class="h3 mt-3">
            {{ trans('group.member_list') }}
        </label>
        <table class="table table-hover table-bordered mb-4 mt-3">
            <thead class="thead-dark">
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">{{ trans('user.name') }}</th>
                    <th scope="col">{{ trans('user.email') }}</th>
                </tr>
            </thead>
            <tbody id="table">
                @isset ($group)
                    @forelse ($group->users as $key => $user)
                        <tr>
                            <td scope="row"> {{ ++$key }} </td>
                            <td>
                                <label class="font-weight-bold">
                                    {{ $user->name }}
                                </label>
                                @if (!empty($leader) && $user->email == $leader->email)
                                    <div class="badge badge-success">
                                        {{ trans('group.leader') }}
                                    </div>
                                @endif
                            </td>
                            <td>
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

    @isset ($group->project)
        <div class="project-infor mt-2">
            <label class="font-weight-bold">{{ trans('group.name_project') }}: {{ $group->project->name }}</label><br>
            <label class="font-weight-bold">{{ trans('group.des_project') }}: {{ $group->project->description }}</label><br>
            <label class="font-weight-bold">{{ trans('group.status_project') }}:
                {{ $group->project->is_accepted == null ||  $group->project->is_accepted == false ? trans('project.pending') : trans('project.approved') }}
            </label><br>
            <span><a href="{{ route('projects.show', $group->project->id) }}">{{ trans('general.details') }}</a></span>
        </div>
    @else
        @if ($leader && auth()->user()->id == $leader->id)
            <a class="btn btn-success"  href="{{ route('groups.projects.create', $group->id) }}">
                {{ trans('group.addProject') }}
            </a>
        @else
            <p>{{ trans('general.empty', ['attribute' => trans('project.project')]) }}</p>
        @endif
    @endisset
@endsection
