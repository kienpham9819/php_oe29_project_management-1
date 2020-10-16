@extends('users.admin.index')

@section('breadcrumb')
    <li class="breadcrumb-item text-capitalize" aria-current="page">
       <a href="{{ route('users.index') }}">{{ trans('user.title_list') }}</a>
    </li>
    <li class="breadcrumb-item active text-capitalize" aria-current="page">
        {{ trans('user.edit') . '-' . $user->name }}
    </li>
@endsection

@section('active-user', 'text-primary')
@section('active-role', 'text-dark')
@section('active-course', 'text-dark')

@section('admin')
    <label class="h3 text-uppercase">
        {{ trans('user.edit_title') }}
    </label>
    <form action="{{ route('users.update', $user->id) }}" method="POST" role="form">
        @csrf
        @method('patch')
        <div class="form-group">
            <label>{{ trans('user.name') }}</label>
            @error('name')
                <span class="text-danger">{{ $message }}</span>
            @enderror
            <input type="text"
                class="form-control"
                name="name"
                placeholder="{{ trans('user.type_name') }}"
                value="{{ old('name', $user->name) }}">
        </div>

        <div class="form-group">
            <label>{{ trans('user.email') }}</label>
            @error('email')
                <span class="text-danger">{{ $message }}</span>
            @enderror
            <input type="email"
                class="form-control"
                name="email"
                placeholder="{{ trans('user.type_email') }}"
                value="{{ old('email', $user->email) }}">
        </div>

        <div class="form-group">
            <label>{{ trans('user.password') }}</label>
            @error('password')
                <span class="text-danger">{{ $message }}</span>
            @enderror
            <input type="password"
                class="form-control"
                name="password"
                placeholder="{{ trans('user.type_password') }}">
        </div>

        <div class="form-group">
            <label>{{ trans('user.password_confirm') }}</label>
            @error('password_confirm')
                <span class="text-danger">{{ $message }}</span>
            @enderror
            <input type="password"
                class="form-control"
                name="password_confirm"
                placeholder="{{ trans('user.retype_password') }}">
        </div>

        <div class="form-group">
            @error('roles')
                <span class="text-danger">{{ $message }}</span>
            @enderror
            <br>
            <div class="table-responsive">
                <table class="table table-hover table-bordered">
                    <thead class="thead-dark">
                        <tr>
                            <th>#</th>
                            <th>{{ trans('role.name') }}</th>
                            <th>{{ trans('role.option') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @isset ($roles)
                            @foreach ($roles as $key => $role)
                                <tr>
                                    <td>{{ ++$key }}</td>
                                    <td>
                                        <label for="{{ $role->slug }}">
                                            {{ $role->name }}
                                        </label>
                                    </td>
                                    <td>
                                        <input type="checkbox"
                                            id="{{ $role->slug }}"
                                            @if ($user->roles->contains($role))
                                                checked
                                            @endif
                                            name="roles[{{ $role->slug }}]"
                                            value="{{ $role->id }}">
                                        @error('roles.' . $role->slug)
                                            <span class="text-danger">
                                                {{ $message }}
                                            </span>
                                        @enderror
                                    </td>
                                </tr>
                            @endforeach
                        @endisset
                    </tbody>
                </table>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">{{ trans('user.save') }}</button>
    </form>
@endsection
