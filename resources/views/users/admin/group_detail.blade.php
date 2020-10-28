@extends('users.admin.index')

@section('breadcrumb')
    <li class="breadcrumb-item text-capitalize" aria-current="page">
       <a href="{{ route('courses.index') }}">{{ trans('course.title_list') }}</a>
    </li>
    <li class="breadcrumb-item text-capitalize" aria-current="page">
        <a href="{{ route('courses.show', $group->course_id) }}">{{ $group->course_id }}</a>
    </li>
    <li class="breadcrumb-item active text-capitalize" aria-current="page">
        {{ trans('group.detail') . '-' . $group->name }}
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
    <label>
        <h5><span class="font-weight-bold">{{ trans('group.group') }} :</span> <span>{{ $group->name }}</span></h5>
        <h5><span class="font-weight-bold">{{ trans('group.leader') }} :</span>
        <span>
            {{ $leader->name ?? '#' }}
        </span></h5>
    </label><br>
    <button class="btn btn-primary" type="button" data-toggle="dropdown">
        <i class="fas fa-user-plus"></i>{{ trans('course.add_member_form') }}
    </button>
    @error('user_id')
        <span class="text-danger pl-2">{{ $message }}</span>
    @enderror
    <ul class="dropdown-menu h-50 p-2 overflow-auto">
        <form action="{{ route('groups.addUser', [$group->id]) }}" method="post">
            @csrf
            <input class="form-control" id="search_user" type="text" placeholder="{{ trans('course.search_user') }}">
            @forelse ($users as $user)
                <li class="pl-2">
                    <input type="checkbox" value="{{ $user->id }}" name="user_id[]" id="{{ $user->id }}">
                    <label class="ml-2 mr-2">{{ $user->email }}</label>
                </li>
            @empty
                <p>{{ trans('general.empty', ['attribute' => trans('user.user')]) }}</p>
            @endforelse
            <input type="submit" value="Add" class="form-control btn btn-primary">
        </form>
    </ul>
    <div id="user-list">
        <label class="h3 mt-3">
            {{ trans('group.member_list') }}
        </label>
        <form action="{{ route('groups.addLeader', [$group->id]) }}" method="post">
            @csrf
            <table class="table table-hover table-bordered mb-4 mt-3">
                <thead class="thead-dark">
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">{{ trans('user.name') }}</th>
                        <th scope="col">{{ trans('user.email') }}</th>
                        <th scope="col">{{ trans('group.is_leader') }}</th>
                        <th scope="col">{{ trans('user.delete') }}</th>
                    </tr>
                </thead>
                <tbody id="table">
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
                            <td>
                                <input type="radio" required name="leader"
                                    @if (!empty($leader) && $leader->id == $user->id) checked @endif value="{{ $user->id }}">
                            </td>
                            <td>
                                <a data-toggle="modal" href='#delete{{ $user->id }}'>
                                    <i class="fas fa-user-minus text-danger"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <p>{{ trans('general.empty', ['attribute' => trans('user.user')]) }}</p>
                    @endforelse
                </tbody>
            </table>
            <button type="submit" class="btn btn-primary mb-3">{{ trans('general.save') }}</button>
            @error('addLeader')
                <span class="text-danger"> {{ $message }} </span>
            @enderror
        </form>

        @forelse ($group->users as $user)
            <div class="modal fade" id="{{ 'delete' . $user->id }}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
                            <form action="{{ route('groups.deleteUser', [$group, $user->id]) }}" method="post">
                                @csrf
                                @method('delete')
                                <button type="submit" class="btn btn-danger">{{ trans('general.delete') }}</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @empty
        @endforelse
    </div>

    @isset ($group->project)
        <div class="project-infor mt-2">
            <hr>
            <label class="font-weight-bold mr-2">{{ trans('group.name_project') }} :</label><span>{{ $group->project->name }}</span><br>
            <hr>
            <label class="font-weight-bold mr-2">{{ trans('group.des_project') }} :</label><span>{{ $group->project->description }}</span><br>
            <hr>
            <label class="font-weight-bold mr-2">{{ trans('group.status_project') }} :</label>
            <span>
                {{ $group->project->is_accepted == null ||  $group->project->is_accepted == false ? trans('project.pending') : trans('project.approved') }}
            </span>
            <br>
            <hr>
            <span>
                <a href="{{ route('projects.show', $group->project->id) }}">
                    <i class="fas fa-angle-double-right"></i>
                    {{ trans('general.details') }}
                    <i class="fas fa-angle-double-right"></i>
                </a>
            </span>
        </div>
    @else
        <p>{{ trans('general.empty', ['attribute' => trans('project.project')]) }}</p>
    @endisset

@endsection
