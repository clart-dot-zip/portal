<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('images/clart.png') }}">

    <title>@yield('title', config('app.name', 'Portal'))</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="hold-transition login-page" style="min-height: 100vh;">
    <div class="login-box">
        <div class="login-logo">
            <a href="{{ route('welcome') }}">
                <b>{{ config('app.name', 'Portal') }}</b>
            </a>
        </div>

        <div class="card">
            <div class="card-body login-card-body">
                @hasSection('content')
                    @yield('content')
                @else
                    {{ $slot ?? '' }}
                @endif
            </div>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
