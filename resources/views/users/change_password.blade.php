@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center text-center">
            <div class="bg-white border p-5 w-50 ml-5 mt-5">
                <div class="row justify-content-center">
                    <label class="h2 font-weight-bold text-uppercase">
                        {{ trans('user.change_password') }}
                    </label>
                </div>
                <hr>
                <form action="{{ route('update_password') }}" method="POST" role="form">
                    @csrf
                    @method('patch')
                    <div class="form-group">
                        <input type="text" id="name"
                            class="form-control rounded-pill"
                            disabled
                            value="{{ $user->name }}">
                    </div>

                    <div class="form-group">
                        <input type="email"
                            class="form-control rounded-pill"
                            disabled
                            value="{{ $user->email }}">
                    </div>

                    <div class="form-group">
                        <input type="password"
                            class="form-control rounded-pill @error('old_password') is-invalid @enderror"
                            name="old_password"
                            placeholder="{{ trans('user.old_password') }}">
                        @error('old_password')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <input type="password"
                            class="form-control rounded-pill @error('password') is-invalid @enderror"
                            name="password"
                            placeholder="{{ trans('user.new_password') }}">
                        @error('password')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <input type="password"
                            class="form-control rounded-pill @error('password_confirm') is-invalid @enderror"
                            name="password_confirm"
                            placeholder="{{ trans('user.retype_password') }}">
                        @error('password_confirm')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-outline-primary text-uppercase w-100">{{ trans('user.save') }}</button>
                </form>
                <hr>
                <a class="btn btn-outline-dark w-100 text-uppercase" href="{{ route('social.auth', ['github']) }}">
                    <i class="fab fa-github"></i>
                    {{ trans('user.link_github') }}
                </a>
            </div>
        </div>
    </div>
@endsection
