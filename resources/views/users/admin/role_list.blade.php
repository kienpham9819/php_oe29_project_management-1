@extends('users.admin.index')

@section('breadcrumb')
    <li class="breadcrumb-item active text-capitalize" aria-current="page">
       {{ trans('role.list') }}
    </li>
@endsection

@section('active-user', 'text-dark')
@section('active-role', 'text-primary')
@section('active-course', 'text-dark')

@section('admin')
    @if (session('message'))
        <div class="alert alert-success noti">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <strong>{{ session('message') }}</strong>
        </div>
    @endif
    <div id="user-list">
        <label class="h3">
            {{trans('role.role_list')}}
        </label>
        <table class="table table-hover table-bordered mb-4 mt-3">
            <thead class="thead-dark">
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">{{ trans('role.name') }}</th>
                    <th scope="col">{{ trans('role.slug') }}</th>
                    <th scope="col">{{ trans('role.edit') }}</th>
                </tr>
            </thead>
            <tbody id="table">
                @forelse ($roles as $key => $role)
                    <tr>
                        <td scope="row"> {{ ++$key }} </td>
                        <td>
                            <label class="font-weight-bold">
                                {{ $role->name }}
                            </label>
                        </td>
                        <td>
                            {{ $role->slug }}
                        </td>
                        <td>
                            <a href="{{ route('roles.edit', $role->id) }}">
                                <i class="fas fa-user-tag text-primary"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <p>{{ trans('general.empty', ['attribute' => trans('role.role')]) }}</p>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
