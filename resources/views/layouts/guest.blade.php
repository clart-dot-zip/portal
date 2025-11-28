<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('images/clart.png') }}">

    <title>@yield('title', config('app.name', 'Portal'))</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="h-full bg-fluent-neutral-8 flex items-center justify-center">
    <div class="w-full max-w-md p-6">
        {{-- Microsoft-style Logo --}}
        <div class="text-center mb-8">
            <a href="{{ route('welcome') }}" class="inline-block">
                <img src="{{ asset('images/clart.png') }}" alt="{{ config('app.name', 'Portal') }}" class="w-16 h-16 mx-auto mb-3">
                <h1 class="text-2xl font-semibold text-fluent-neutral-30">{{ config('app.name', 'Portal') }}</h1>
            </a>
        </div>

        {{-- Login Card --}}
        <div class="fluent-card bg-white">
            <div class="p-8">
                @hasSection('content')
                    @yield('content')
                @else
                    {{ $slot ?? '' }}
                @endif
            </div>
        </div>

        {{-- Footer Links --}}
        <div class="text-center mt-6">
            <p class="text-xs text-fluent-neutral-26">
                &copy; {{ now()->year }} {{ config('app.name', 'Portal') }}. All rights reserved.
            </p>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
