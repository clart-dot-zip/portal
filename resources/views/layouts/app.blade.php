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
@php
    $user = Auth::user();
    $isPortalAdmin = request()->attributes->get('isPortalAdmin', view()->shared('isPortalAdmin', false));
    $sidebarMenu = [
        [
            'label' => 'Dashboard',
            'icon' => 'fas fa-tachometer-alt',
            'route' => 'dashboard',
            'active' => request()->routeIs('dashboard'),
            'visible' => true,
        ],
        [
            'label' => 'Users',
            'icon' => 'fas fa-users',
            'route' => 'users.index',
            'active' => request()->routeIs('users.*'),
            'visible' => $isPortalAdmin,
        ],
        [
            'label' => 'Groups',
            'icon' => 'fas fa-layer-group',
            'route' => 'groups.index',
            'active' => request()->routeIs('groups.*'),
            'visible' => $isPortalAdmin,
        ],
        [
            'label' => 'Applications',
            'icon' => 'fas fa-th-large',
            'route' => 'applications.index',
            'active' => request()->routeIs('applications.*'),
            'visible' => $isPortalAdmin,
        ],
        [
            'label' => 'Git Management',
            'icon' => 'fas fa-code-branch',
            'route' => 'git-management.index',
            'active' => request()->routeIs('git-management.*'),
            'visible' => $isPortalAdmin,
        ],
        [
            'label' => 'PIM',
            'icon' => 'fas fa-id-card-alt',
            'route' => 'pim.index',
            'active' => request()->routeIs('pim.*'),
            'visible' => $isPortalAdmin,
        ],
    ];
@endphp
<body class="hold-transition sidebar-mini layout-fixed">
    <div id="portalPreloader" class="preloader flex-column justify-content-center align-items-center">
        <img class="animation__shake" src="{{ asset('images/clart.png') }}" alt="{{ config('app.name', 'Portal') }}" height="60" width="60">
        <p class="mt-2 text-muted">Loading Portal…</p>
        <div class="portal-loading-bar mt-2"></div>
    </div>

    <div class="wrapper">
        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
                </li>
                <li class="nav-item d-none d-sm-inline-block">
                    <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">Dashboard</a>
                </li>
            </ul>

            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" data-widget="fullscreen" href="#" role="button">
                        <i class="fas fa-expand-arrows-alt"></i>
                    </a>
                </li>
                @auth
                    <li class="nav-item dropdown">
                        <a class="nav-link" data-toggle="dropdown" href="#">
                            <i class="far fa-user"></i>
                            <span class="ml-1">{{ $user->name ?? $user->username }}</span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                            <span class="dropdown-item dropdown-header">
                                {{ $user->name ?? $user->username }}
                                <small class="d-block text-muted">{{ $user->email }}</small>
                            </span>
                            <div class="dropdown-divider"></div>
                            <a href="{{ route('users.profile') }}" class="dropdown-item">
                                <i class="fas fa-id-badge mr-2"></i> Profile
                            </a>
                            <div class="dropdown-divider"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="fas fa-sign-out-alt mr-2"></i> Log Out
                                </button>
                            </form>
                        </div>
                    </li>
                @endauth
            </ul>
        </nav>
        <!-- /.navbar -->

        <!-- Main Sidebar Container -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <a href="{{ route('dashboard') }}" class="brand-link">
                <img src="{{ asset('images/clart.png') }}" alt="Logo" class="brand-image img-circle elevation-3" style="opacity: .9">
                <span class="brand-text font-weight-light">{{ config('app.name', 'Portal') }}</span>
            </a>

            <div class="sidebar">
                @auth
                    <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                        <div class="image">
                            <img src="https://www.gravatar.com/avatar/{{ md5(strtolower(trim($user->email ?? 'portal@example.com'))) }}?s=80&d=mp" class="img-circle elevation-2" alt="User Image">
                        </div>
                        <div class="info">
                            <a href="{{ route('users.profile') }}" class="d-block">{{ $user->name ?? $user->username }}</a>
                            <span class="text-muted d-block text-xs">{{ $user->email }}</span>
                        </div>
                    </div>
                @endauth

                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                        @foreach($sidebarMenu as $item)
                            @continue(!$item['visible'])
                            <li class="nav-item">
                                <a href="{{ route($item['route']) }}" class="nav-link {{ $item['active'] ? 'active' : '' }}">
                                    <i class="nav-icon {{ $item['icon'] }}"></i>
                                    <p>{{ $item['label'] }}</p>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </nav>
            </div>
        </aside>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        @isset($header)
                            <div class="col-sm-12">
                                {{ $header }}
                            </div>
                        @else
                            <div class="col-sm-6">
                                <h1 class="m-0">@yield('page_title', 'Dashboard')</h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                                    <li class="breadcrumb-item active">@yield('page_title', 'Dashboard')</li>
                                </ol>
                            </div>
                        @endisset
                    </div>
                </div>
            </section>

            <section class="content">
                <div id="mainContent" class="container-fluid" style="opacity: 0; transition: opacity .5s ease-in;">
                    @if(session('status'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('status') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @hasSection('content')
                        @yield('content')
                    @endif

                    {{ $slot ?? '' }}
                </div>
            </section>
        </div>

        <footer class="main-footer">
            <strong>&copy; {{ now()->year }} {{ config('app.name', 'Portal') }}.</strong>
            <div class="float-right d-none d-sm-inline-block">
                <b>Version</b> 1.0.0
            </div>
        </footer>

        <aside class="control-sidebar control-sidebar-dark"></aside>
    </div>

    <script>
        class LoadingManager {
            constructor() {
                this.loadingOverlay = document.getElementById('portalPreloader');
                this.mainContent = document.getElementById('mainContent');
                this.progress = 0;
                this.loaded = false;

                this.startLoading();
            }

            startLoading() {
                this.updateProgress(20);

                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', () => this.updateProgress(60));
                } else {
                    this.updateProgress(60);
                }

                if (document.readyState === 'complete') {
                    setTimeout(() => this.completeLoading(), 350);
                } else {
                    window.addEventListener('load', () => {
                        this.updateProgress(90);
                        setTimeout(() => this.completeLoading(), 300);
                    });
                }

                setTimeout(() => {
                    if (!this.loaded) {
                        this.completeLoading();
                    }
                }, 5000);
            }

            updateProgress(targetProgress) {
                if (this.loaded || !this.loadingOverlay) return;
                this.progress = Math.min(100, Math.max(this.progress, targetProgress));
            }

            completeLoading() {
                if (this.loaded) return;
                this.loaded = true;

                if (this.loadingOverlay) {
                    this.loadingOverlay.style.opacity = '0';
                    this.loadingOverlay.style.transition = 'opacity .4s ease-out';
                    setTimeout(() => this.loadingOverlay.style.display = 'none', 400);
                }

                if (this.mainContent) {
                    this.mainContent.style.opacity = '1';
                }
            }

            forceComplete() {
                this.completeLoading();
            }
        }

        window.loadingManager = new LoadingManager();
    </script>

    @stack('scripts')
</body>
</html>
