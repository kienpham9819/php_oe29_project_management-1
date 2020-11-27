<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>
    <script src="{{ asset('js/Chart.js') }}" defer></script>
    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-primary border-bottom">
            <div class="container">
                <img src="{{ asset(config('app.logo')) }}">
                <span class="ml-2 mr-2 text-white font-weight-bold"> | </span>
                <a class="navbar-brand text-white font-weight-bold text-uppercase" href="#">
                    {{ config('app.name') }}
                </a>
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item dropdown mr-5">
                        <a id="managingDropdown" class="nav-link dropdown-toggle text-white lang" href= "#" role="button"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                            <i class="fas fa-globe"></i>
                            {{ trans('general.language') }}
                            <span class="caret"></span>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="managingDropdown">
                            <a class="dropdown-item" id="en" href="{{ route('localization', ['en']) }}">
                                {{ trans('general.english') }}
                            </a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" id="vi" href="{{ route('localization', ['vi']) }}">
                                {{ trans('general.vietnamese') }}
                            </a>
                        </div>
                    </li>
                    @guest
                        <li class="nav-item dropdown mr-5">
                            <a id="managingDropdown" class="nav-link text-white" href="{{ route('login') }}" role="button" v-pre>
                                {{ trans('general.login') }}
                                <span class="caret"></span>
                            </a>
                        </li>
                    @else
                        <li class="nav-item dropdown mr-5">
                            <a id="managingDropdown" class="nav-link dropdown-toggle text-white" href="#" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                {{ auth()->user()->name }}
                                <span class="caret"></span>
                            </a>
                            <div class="dropdown-menu" aria-labelledby="managingDropdown">
                                <a class="dropdown-item" href="{{ route('change_password') }}">
                                    <i class="fas fa-user-cog"></i>
                                    {{ trans('general.setting') }}
                                </a>
                                <div class="dropdown-divider"></div>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button class="dropdown-item">
                                        <i class="fas fa-sign-out-alt"></i>
                                        {{ trans('general.logout') }}
                                    </button>
                                </form>
                            </div>
                        </li>
                        <li class="nav-item dropdown dropdown-notifications">
                            <a id="navbarDropdown" class="nav-link dropdown-toggle text-light" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                <i class="far fa-bell text-light"></i>
                                <span id="qt" class="text-danger"> {{ Auth::user()->unreadNotifications->count() }} </span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right menu-notification" aria-labelledby="navbarDropdown">
                                <input id="userId" type="hidden" name="idUser" value="{{ auth()->user()->id }}">
                                @forelse (Auth::user()->unreadNotifications as $notification)
                                    <a class="dropdown-item p-2 d-block" href="#">
                                        <span>{{ $notification->data['tasklistName'] }}</span><br>
                                    </a>
                                @empty
                                    <span id="none-content" class="p-2 d-block w-auto">{{ 'khong co thong bao nao' }}</span>
                                @endforelse
                            </div>
                        </li>
                    @endguest
                </ul>
            </div>
        </nav>

        <main class="py-4">
            @yield('content')
        </main>
    </div>
</body>
</html>
