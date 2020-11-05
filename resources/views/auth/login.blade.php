@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center text-center">
            <div class="bg-white border p-5 w-50 ml-5 mt-5">
                <div class="row justify-content-center">
                    <label class="h2 font-weight-bold text-uppercase">
                        {{ trans('login.title') }}
                    </label>
                </div>
                <hr>
                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="input-group input-group-lg p-2">
                        <input id="email" type="email"
                            class="form-control rounded-pill pl-4 @error('email') is-invalid @enderror"
                            name="email"
                            placeholder="{{ trans('login.email') }}"
                            value="{{ old('email') }}"
                            required
                            autocomplete="email"
                            autofocus>
                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="input-group input-group-lg p-2">
                        <input id="password" type="password"
                            class="form-control rounded-pill pl-4 @error('password') is-invalid @enderror"
                            name="password"
                            placeholder="{{ trans('login.password') }}"
                            required
                            autocomplete="current-password">
                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox"
                            name="remember"
                            id="remember"
                            {{ old('remember') ? 'checked' : '' }}>

                        <label class="form-check-label" for="remember">
                            {{ trans('login.remember') }}
                        </label>
                    </div>
                    <div class="input-group input-group-lg p-2">
                        <input type="submit"
                            id="login-btn"
                            class="d-none">
                        <label for="login-btn"
                            class="form-control bg-primary text-white text-uppercase rounded-pill pl-4 login-btn">
                            {{ trans('general.login') }}
                        </label>
                    </div>
                </form>
                @if (Route::has('password.request'))
                    <a class="btn btn-link text-uppercase" href="{{ route('password.request') }}">
                        {{ trans('login.forgot_password') }}
                    </a>
                @endif
            </div>

        </div>
    </div>
@endsection
