@extends('users.admin.index')

@section('breadcrumb')
    <li class="breadcrumb-item text-capitalize" aria-current="page">
       <a href="{{ route('roles.index') }}">{{ trans('role.list') }}</a>
    </li>
    <li class="breadcrumb-item active text-capitalize" aria-current="page">
        {{ trans('role.edit') . '-' . $role->slug}}
    </li>
@endsection

@section('active-user', 'text-dark')
@section('active-role', 'text-primary')
@section('active-course', 'text-dark')

@section('admin')
    <label class="h3 text-uppercase">
        {{ trans('role.edit_title') }}
    </label>
    <form action="{{ route('roles.update', $role->id) }}" method="POST" role="form">
        @csrf
        @method('patch')
        <div class="form-group">
            <hr>
            @error('name')
                <span class="text-danger">{{ $message }}</span>
            @enderror
            <input type="text"
                class="form-control"
                name="name"
                placeholder="{{ $role->name }}"
                disabled>
            <hr>
        </div>

        <div class="form-group">
            <label class="text-capitalize">{{ trans('role.permission') }}</label><br>
            @error('permission')
                <span class="text-danger">{{ $message }}</span>
            @enderror
            <br>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ trans('role.name') }}</th>
                            <th>{{ trans('role.option') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($permissions as $key => $permission)
                            <tr>
                                <td>{{ ++$key }}</td>
                                <td>
                                    <label for="{{ $permission->slug }}">
                                        {{ $permission->name }}
                                    </label>
                                </td>
                                <td>
                                    <input type="checkbox"
                                        id="{{ $permission->slug }}"
                                        name="permission[]"
                                        @if ($role->permissions->contains($permission))
                                            checked
                                        @endif
                                        value="{{ $permission->id }}">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">{{ trans('role.save') }}</button>
    </form>
@endsection
