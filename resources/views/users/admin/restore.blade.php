@extends('users.admin.index')

@section('breadcrumb')
    <li class="breadcrumb-item active text-capitalize" aria-current="page">
       {{ trans('user.title_list') }}
    </li>
@endsection

@section('admin')
    @if (session('message'))
        <div class="alert alert-success">
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
                    <input type="text" class="form-control" name="name" id="name_user" placeholder="{{ trans('user.type_name') }}" value="{{ old('name') }}">
                </div>
                <div class="form-group">
                    <label for="email_user">{{ trans('user.email') }}</label>
                    @error('email')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                    <input type="email" class="form-control" name="email" id="email_user" placeholder="{{ trans('user.type_email') }}" value="{{ old('email') }}">
                </div>
                <div class="form-group">
                    <label for="password_user">{{ trans('user.password') }}</label>
                    @error('password')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                    <input type="password" class="form-control" name="password" id="password_user" placeholder="{{ trans('user.type_password') }}" value="{{ old('password') }}">
                </div>
                <div class="form-group">
                    <label for="repassword">{{ trans('user.password_confirm') }}</label>
                    @error('password_confirm')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                    <input type="password" class="form-control" name="password_confirm" id="repassword" placeholder="{{ trans('user.retype_password') }}" value="{{ old('password_confirm') }}">
                </div>
                <button type="submit" class="btn btn-primary">{{ trans('user.save') }}</button>
            </form>
            <hr>
            <form action="" method="post">
                @csrf
                <div class="input-group mb-3">
                    <input type="file" name="file" aria-label="file" accept=".csv,.xlsx,.xls">
                </div>
                <input type="submit" class="btn btn-success" value="Import">
            </form>
        </div>
    </div>
    <hr>
    <div id="user-list">
        <label class="h3">
            {{ trans('user.disable_list') }}
        </label>
        <table class="table table-hover table-bordered mb-4 mt-3">
            <thead class="thead-dark">
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">{{ trans('user.name') }}</th>
                    <th scope="col">{{ trans('user.email') }}</th>
                    <th scope="col">{{ trans('user.restore') }}</th>
                    <th scope="col">{{ trans('user.force_delete') }}</th>
                </tr>
            </thead>
            <tbody id="table">
                @forelse ($users as $key => $user)
                    <tr>
                        <td scope="row"> {{ ++$key }} </td> <!--#-->
                        <td>  <!--Title-->
                            <label class="font-weight-bold">
                                {{ $user->name }}
                            </label>
                            @isset ($user->deleted_at)
                                <div class="badge badge-danger">
                                    {{ trans('user.deleted') }}
                                </div>
                            @endisset
                        </td>
                        <td>
                            {{ $user->email }}
                        </td>
                        <td>
                            <a href="{{ route('users.restore', $user->id) }}">
                                <i class="fas fa-trash-restore text-success"></i>
                            </a>
                        </td>
                        <td>
                            <form action="{{ route('users.forceDelete', $user->id) }}" method="post">
                                @csrf
                                @method('delete')
                                <button type="submit" class="border-0 bg-white">
                                    <i class="fas fa-trash text-danger"></i>
                                </button>
                            </form>
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

