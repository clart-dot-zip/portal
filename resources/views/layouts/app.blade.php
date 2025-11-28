<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('images/clart.png') }}">

    <title>@yield('title', config('app.name', 'Portal'))</title>

    @vite(['resources/css/app.css', 'resources/css/dashboard.css', 'resources/js/app.js'])
    @stack('head')
</head>
@php
    $user = Auth::user();
    $isPortalAdmin = request()->attributes->get('isPortalAdmin', view()->shared('isPortalAdmin', false));
    $sidebarMenu = [
        [
            'label' => 'Dashboard',
            'icon' => '<svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor"><path d="M3 3a2 2 0 012-2h4a2 2 0 012 2v4a2 2 0 01-2 2H5a2 2 0 01-2-2V3zm0 10a2 2 0 012-2h4a2 2 0 012 2v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4zM13 3a2 2 0 012-2h4a2 2 0 012 2v4a2 2 0 01-2 2h-4a2 2 0 01-2-2V3zm0 10a2 2 0 012-2h4a2 2 0 012 2v4a2 2 0 01-2 2h-4a2 2 0 01-2-2v-4z"/></svg>',
            'route' => 'dashboard',
            'active' => request()->routeIs('dashboard'),
            'visible' => true,
        ],
        [
            'label' => 'Users',
            'icon' => '<svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor"><path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zm2 0a3 3 0 116 0 3 3 0 01-6 0zM5 11a5 5 0 015 5v1H0v-1a5 5 0 015-5zm10 0a5 5 0 015 5v1h-10v-1a5 5 0 015-5z"/></svg>',
            'route' => 'users.index',
            'active' => request()->routeIs('users.*'),
            'visible' => $isPortalAdmin,
        ],
        [
            'label' => 'Groups',
            'icon' => '<svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M3 3a2 2 0 012-2h10a2 2 0 012 2v3.586l-1-1V3H5v10h4.586l-1 1H5a2 2 0 01-2-2V3zm14 4a1 1 0 011 1v8a1 1 0 01-1 1h-5a1 1 0 01-1-1v-8a1 1 0 011-1h5z" clip-rule="evenodd"/></svg>',
            'route' => 'groups.index',
            'active' => request()->routeIs('groups.*'),
            'visible' => $isPortalAdmin,
        ],
        [
            'label' => 'Applications',
            'icon' => '<svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor"><path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zm10 0a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/></svg>',
            'route' => 'applications.index',
            'active' => request()->routeIs('applications.*'),
            'visible' => $isPortalAdmin,
        ],
        [
            'label' => 'Git Management',
            'icon' => '<svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M12.316 3.051a1 1 0 01.633 1.265l-4 12a1 1 0 11-1.898-.632l4-12a1 1 0 011.265-.633zM5.707 6.293a1 1 0 010 1.414L3.414 10l2.293 2.293a1 1 0 11-1.414 1.414l-3-3a1 1 0 010-1.414l3-3a1 1 0 011.414 0zm8.586 0a1 1 0 011.414 0l3 3a1 1 0 010 1.414l-3 3a1 1 0 11-1.414-1.414L16.586 10l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>',
            'route' => 'git-management.index',
            'active' => request()->routeIs('git-management.*'),
            'visible' => $isPortalAdmin,
        ],
        [
            'label' => 'PIM',
            'icon' => '<svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 2a1 1 0 00-1 1v1a1 1 0 002 0V3a1 1 0 00-1-1zM4 4h3a3 3 0 006 0h3a2 2 0 012 2v9a2 2 0 01-2 2H4a2 2 0 01-2-2V6a2 2 0 012-2zm2.5 7a1.5 1.5 0 100-3 1.5 1.5 0 000 3zm2.45 4a2.5 2.5 0 10-4.9 0h4.9zM12 9a1 1 0 100 2h3a1 1 0 100-2h-3zm-1 4a1 1 0 011-1h2a1 1 0 110 2h-2a1 1 0 01-1-1z" clip-rule="evenodd"/></svg>',
            'route' => 'pim.index',
            'active' => request()->routeIs('pim.*'),
            'visible' => $isPortalAdmin,
        ],
    ];
@endphp

<body class="h-full bg-fluent-neutral-6" x-data="{ sidebarExpanded: $store.navigation.sidebarExpanded }">
    <!-- Fluent UI Loading Overlay -->
    <div id="fluentPreloader" class="fixed inset-0 bg-white z-50 flex flex-col items-center justify-center transition-opacity duration-300">
        <img src="{{ asset('images/clart.png') }}" alt="{{ config('app.name', 'Portal') }}" class="w-16 h-16 mb-4 animate-pulse">
        <p class="text-sm text-fluent-neutral-26 mb-3">Loading {{ config('app.name', 'Portal') }}...</p>
        <div class="fluent-spinner w-8 h-8"></div>
    </div>

    <!-- Azure Portal AppShell Layout -->
    <div class="h-full flex flex-col">
        <!-- Top Command Bar -->
        <div class="fluent-command-bar bg-white border-b border-fluent-neutral-14 h-11 flex-shrink-0 z-30">
            <div class="flex items-center justify-between h-full px-2">
                <!-- Left: Brand & Navigation Toggle -->
                <div class="flex items-center gap-2">
                    <button 
                        @click="$store.navigation.toggle()"
                        class="fluent-button-secondary px-2 py-1 rounded hover:bg-fluent-neutral-8"
                        aria-label="Toggle navigation"
                    >
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                    
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2 hover:opacity-80 transition-opacity">
                        <img src="{{ asset('images/clart.png') }}" alt="Logo" class="w-6 h-6">
                        <span class="text-sm font-semibold text-fluent-neutral-30 hidden md:inline">{{ config('app.name', 'Portal') }}</span>
                    </a>
                </div>

                <!-- Right: Search, Notifications, User Menu -->
                <div class="flex items-center gap-2">
                    <!-- Search (placeholder for future) -->
                    <button class="fluent-button-secondary px-2 py-1 rounded hover:bg-fluent-neutral-8 hidden lg:flex items-center gap-2" disabled>
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-xs">Search</span>
                    </button>

                    <!-- User Menu -->
                    @auth
                        <div x-data="{ open: false }" class="relative">
                            <button 
                                @click="open = !open"
                                @click.outside="open = false"
                                class="fluent-button-secondary flex items-center gap-2 px-2 py-1 rounded hover:bg-fluent-neutral-8"
                            >
                                <x-fluent-avatar 
                                    :name="$user->name ?? $user->username"
                                    :image="'https://www.gravatar.com/avatar/' . md5(strtolower(trim($user->email ?? ''))) . '?s=32&d=mp'"
                                    size="small"
                                />
                                <span class="text-sm hidden md:inline">{{ $user->name ?? $user->username }}</span>
                                <svg width="12" height="12" viewBox="0 0 12 12" fill="currentColor" class="transition-transform" :class="{ 'rotate-180': open }">
                                    <path d="M2.5 4.5L6 8l3.5-3.5"/>
                                </svg>
                            </button>

                            <!-- Dropdown Menu -->
                            <div 
                                x-show="open"
                                x-transition:enter="transition ease-out duration-100"
                                x-transition:enter-start="opacity-0 scale-95"
                                x-transition:enter-end="opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-75"
                                x-transition:leave-start="opacity-100 scale-100"
                                x-transition:leave-end="opacity-0 scale-95"
                                class="absolute right-0 mt-2 w-64 bg-white rounded-lg shadow-fluent-shadow-16 border border-fluent-neutral-14 py-2 z-50"
                                style="display: none;"
                            >
                                <div class="px-4 py-3 border-b border-fluent-neutral-10">
                                    <p class="text-sm font-semibold text-fluent-neutral-30">{{ $user->name ?? $user->username }}</p>
                                    <p class="text-xs text-fluent-neutral-26 mt-0.5">{{ $user->email }}</p>
                                </div>
                                
                                <a href="{{ route('users.profile') }}" class="flex items-center gap-3 px-4 py-2 text-sm text-fluent-neutral-30 hover:bg-fluent-neutral-8 transition-colors">
                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                                        <path d="M8 8a3 3 0 100-6 3 3 0 000 6zm2 1a5 5 0 00-4 0A5.002 5.002 0 003 14h10a5.002 5.002 0 00-3-5z"/>
                                    </svg>
                                    My Profile
                                </a>
                                
                                <div class="border-t border-fluent-neutral-10 my-1"></div>
                                
                                <form method="POST" action="{{ route('logout') }}" class="block">
                                    @csrf
                                    <button type="submit" class="w-full flex items-center gap-3 px-4 py-2 text-sm text-fluent-error hover:bg-red-50 transition-colors">
                                        <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                                            <path fill-rule="evenodd" d="M3 3a1 1 0 00-1 1v8a1 1 0 001 1h4a1 1 0 110 2H3a3 3 0 01-3-3V4a3 3 0 013-3h4a1 1 0 110 2H3zm9.293 3.293a1 1 0 011.414 0l2 2a1 1 0 010 1.414l-2 2a1 1 0 01-1.414-1.414L12.586 10H7a1 1 0 110-2h5.586l-.293-.293a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                        </svg>
                                        Sign Out
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endauth
                </div>
            </div>
        </div>

        <!-- Main Content Area with Sidebar -->
        <div class="flex-1 flex overflow-hidden">
            <!-- Left Navigation Rail -->
            <nav 
                class="fluent-nav-rail bg-fluent-neutral-6 border-r border-fluent-neutral-14 transition-all duration-200 flex-shrink-0 overflow-y-auto"
                :class="sidebarExpanded ? 'w-56' : 'w-12'"
            >
                <div class="py-2 px-1.5">
                    @foreach($sidebarMenu as $item)
                        @continue(!$item['visible'])
                        <a 
                            href="{{ route($item['route']) }}" 
                            class="fluent-nav-item mb-1 {{ $item['active'] ? 'active' : '' }}"
                            title="{{ $item['label'] }}"
                        >
                            <span class="flex-shrink-0 w-5 h-5">{!! $item['icon'] !!}</span>
                            <span x-show="sidebarExpanded" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="truncate">
                                {{ $item['label'] }}
                            </span>
                        </a>
                    @endforeach
                </div>
            </nav>

            <!-- Main Content Area -->
            <main class="flex-1 overflow-y-auto bg-fluent-neutral-6">
                <!-- Breadcrumb / Page Header -->
                <div class="bg-white border-b border-fluent-neutral-14 px-6 py-3">
                    @isset($header)
                        {{ $header }}
                    @else
                        <div class="flex items-center justify-between">
                            <div>
                                <h1 class="text-xl font-semibold text-fluent-neutral-30">@yield('page_title', 'Dashboard')</h1>
                                <nav class="flex items-center gap-2 text-xs text-fluent-neutral-26 mt-1">
                                    <a href="{{ route('dashboard') }}" class="hover:text-fluent-brand-60 transition-colors">Home</a>
                                    <svg width="12" height="12" viewBox="0 0 12 12" fill="currentColor" class="opacity-50">
                                        <path d="M4.5 3L7.5 6L4.5 9"/>
                                    </svg>
                                    <span>@yield('page_title', 'Dashboard')</span>
                                </nav>
                            </div>
                            @stack('page-actions')
                        </div>
                    @endisset
                </div>

                <!-- Page Content -->
                <div id="mainContent" class="p-6 fluent-fade-in">
                    @if(session('status'))
                        <div class="fluent-card bg-green-50 border-green-200 mb-4 p-4" role="alert">
                            <div class="flex items-start gap-3">
                                <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor" class="text-fluent-success flex-shrink-0 mt-0.5">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-fluent-success">{{ session('status') }}</p>
                                </div>
                                <button onclick="this.parentElement.parentElement.remove()" class="text-green-600 hover:text-green-800">
                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                                        <path d="M8.707 8l3.647-3.646a.5.5 0 00-.708-.708L8 7.293 4.354 3.646a.5.5 0 10-.708.708L7.293 8l-3.647 3.646a.5.5 0 00.708.708L8 8.707l3.646 3.647a.5.5 0 00.708-.708L8.707 8z"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    @endif

                    @hasSection('content')
                        @yield('content')
                    @endif

                    {{ $slot ?? '' }}
                </div>

                <!-- Footer -->
                <footer class="border-t border-fluent-neutral-14 bg-white px-6 py-3 mt-auto">
                    <div class="flex items-center justify-between text-xs text-fluent-neutral-26">
                        <p>&copy; {{ now()->year }} {{ config('app.name', 'Portal') }}. All rights reserved.</p>
                        <p><span class="font-semibold">Version</span> 1.0.0</p>
                    </div>
                </footer>
            </main>
        </div>
    </div>

    <script>
        // Fluent UI Loading Manager
        class FluentLoadingManager {
            constructor() {
                this.preloader = document.getElementById('fluentPreloader');
                this.mainContent = document.getElementById('mainContent');
                this.loaded = false;

                this.init();
            }

            init() {
                if (document.readyState === 'complete') {
                    this.complete();
                } else {
                    window.addEventListener('load', () => this.complete());
                }

                // Failsafe timeout
                setTimeout(() => {
                    if (!this.loaded) this.complete();
                }, 3000);
            }

            complete() {
                if (this.loaded) return;
                this.loaded = true;

                if (this.preloader) {
                    this.preloader.style.opacity = '0';
                    setTimeout(() => {
                        this.preloader.style.display = 'none';
                    }, 300);
                }
            }
        }

        window.fluentLoadingManager = new FluentLoadingManager();
    </script>

    @stack('scripts')
</body>

