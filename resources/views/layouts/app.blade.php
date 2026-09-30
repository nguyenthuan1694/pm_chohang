<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Laravel'))</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap" rel="stylesheet">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    @stack('styles')
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light app-navbar">
            <div class="container">
                <a class="navbar-brand app-brand" href="{{ url('/') }}" style="display: inline-flex; align-items: center; gap: 0.75rem; text-decoration: none;">
                    <div class="thl-logo-emblem" style="display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <svg width="34" height="34" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect width="40" height="40" rx="10" fill="#193a77"/>
                            <path d="M10 14C10 12.8954 10.8954 12 12 12H23C24.1046 12 25 12.8954 25 14V26C25 27.1046 24.1046 28 23 28H12C10.8954 28 10 27.1046 10 26V14Z" fill="#ffffff" fill-opacity="0.2"/>
                            <path d="M12 23L18 15L24 23H12Z" fill="#ffffff"/>
                            <path d="M22 17H28C29.1046 17 30 17.8954 30 19V26C30 27.1046 29.1046 28 28 28H24V19C24 17.8954 23.1046 17 22 17Z" fill="#ffffff" fill-opacity="0.85"/>
                            <circle cx="16" cy="28" r="2.5" fill="#ffffff"/>
                            <circle cx="26" cy="28" r="2.5" fill="#ffffff"/>
                        </svg>
                    </div>
                    <div style="display: flex; flex-direction: column; text-align: left; line-height: 1.15;">
                        <span style="font-weight: 900; color: #193a77; font-size: 1.05rem; letter-spacing: 0.02em;">TÂN HÒA LỢI</span>
                        <span style="font-weight: 700; color: #5574a6; font-size: 0.7rem; letter-spacing: 0.05em;">PHẦN MỀM CHỞ HÀNG</span>
                    </div>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav me-auto">

                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">
                        <!-- Authentication Links -->
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                </li>
                            @endif

                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main>
            @yield('content')
        </main>
    </div>
</body>
</html>
