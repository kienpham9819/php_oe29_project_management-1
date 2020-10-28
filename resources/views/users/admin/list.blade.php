@extends('users.admin.index')

@section('breadcrumb')
    <li class="breadcrumb-item active text-capitalize" aria-current="page">
       {{ trans('user.title_list') }}
    </li>
@endsection

@section('active-user', 'text-primary')
@section('active-role', 'text-dark')
@section('active-course', 'text-dark')

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
        <i class="fas fa-user-plus"></i>
        {{ trans('user.add_user_form') }}
    </a>
    <div class="collapse @if ($errors->any()) show @endif" id="collapseExample">
        <div class="card card-body">
            <form action="{{ route('users.store') }}" method="POST" role="form">
                @csrf
                <div class="form-group">
                    <label for="name_user">{{ trans('user.name') }}</label>
                    @error('name')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                    <input type="text"
                        class="form-control"
                        name="name" id="name_user"
                        placeholder="{{ trans('user.type_name') }}"
                        value="{{ old('name') }}">
                </div>
                <div class="form-group">
                    <label for="email_user">{{ trans('user.email') }}</label>
                    @error('email')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                    <input type="email"
                        class="form-control"
                        name="email" id="email_user"
                        placeholder="{{ trans('user.type_email') }}"
                        value="{{ old('email') }}">
                </div>
                <div class="form-group">
                    <label for="password_user">{{ trans('user.password') }}</label>
                    @error('password')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                    <input type="password"
                        class="form-control"
                        name="password"
                        id="password_user"
                        placeholder="{{ trans('user.type_password') }}"
                        value="{{ old('password') }}">
                </div>
                <div class="form-group">
                    <label for="repassword">{{ trans('user.password_confirm') }}</label>
                    @error('password_confirm')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                    <input type="password"
                        class="form-control"
                        name="password_confirm"
                        id="repassword"
                        placeholder="{{ trans('user.retype_password') }}"
                        value="{{ old('password_confirm') }}">
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
            <hr>
            @if ($errors->any())
                @foreach ($errors->all() as $error)
                    <span class="text-danger">{{ $error }}</span></br>
                @endforeach
            @endif
            <form action="{{ route('users.import') }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="input-group mb-3">
                    <input type="file" name="file" required aria-label="file" accept=".csv,.xlsx,.xls">
                </div>
                <input type="submit" class="btn btn-success" value="{{ trans('user.import') }}">
            </form>
        </div>
    </div>
    <hr>
    <div id="user-list">
        <label class="h3">
            {{ trans('user.active_list') }}
        </label>
        <table class="table table-hover table-bordered mb-4 mt-3">
            <thead class="thead-dark">
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">{{ trans('user.name') }}</th>
                    <th scope="col">{{ trans('user.email') }}</th>
                    <th scope="col">{{ trans('user.edit') }}</th>
                    <th scope="col">{{ trans('user.delete') }}</th>
                </tr>
            </thead>
            <tbody id="table">
                @forelse ($users as $key => $user)
                    <tr>
                        <td scope="row"> {{ ++$key }} </td>
                        <td>
                            <label class="font-weight-bold">
                                {{ $user->name }}
                            </label>
                            @isset ($user->deleted_at)
                                <div class="badge badge-danger">
                                    {{ trans('general.delete') }}
                                </div>
                            @endisset
                        </td>
                        <td>
                            {{ $user->email }}
                        </td>
                        <td>
                            <a href="{{ route('users.edit', $user->id) }}">
                                <i class="fas fa-user-edit text-success"></i>
                            </a>
                        </td>
                        <td>
                            <a data-toggle="modal" href='#{{ $user->id == config("admin.admin") ? "#" : "delete" . $user->id }}'>
                                <i class="fas fa-user-minus text-danger"></i>
                            </a>
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
                                            <form action="{{ route('users.destroy', $user->id) }}"method="post">
                                                @csrf
                                                @method('delete')
                                                <button type="submit" class="btn btn-danger">{{ trans('general.delete') }}</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <p>{{ trans('general.empty', ['attribute' => trans('user.user')]) }}</p>
                @endforelse
            </tbody>
        </table>
        {{ $users->links() }}
    </div>
@endsection

