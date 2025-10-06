<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
    <link rel="icon" type="image/png" href="{{ asset('images/clart.png') }}">
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', config('app.name', 'Laravel'))</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <!-- Loading Overlay -->
        <div id="loadingOverlay" class="fixed inset-0 bg-white z-50 flex items-center justify-center">
            <div class="text-center">
                <!-- Spinning Logo/Icon -->
                <div class="mb-4">
                    <div class="w-16 h-16 mx-auto animate-spin">
                        <svg class="w-full h-full text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zm0 4a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zm8 0a1 1 0 011-1h4a1 1 0 011 1v6a1 1 0 01-1 1h-4a1 1 0 01-1-1v-6z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                </div>
                
                <!-- Loading Text -->
                <div class="text-lg font-semibold text-gray-700 mb-2">Loading Dashboard</div>
                <div class="text-sm text-gray-500">Fetching your data...</div>
                
                <!-- Progress Bar -->
                <div class="mt-4 w-64 mx-auto">
                    <div class="bg-gray-200 rounded-full h-1.5">
                        <div id="loadingProgress" class="bg-blue-600 h-1.5 rounded-full transition-all duration-300 ease-out" style="width: 0%"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main id="mainContent" style="opacity: 0; transition: opacity 0.5s ease-in;">
                @hasSection('content')
                    @yield('content')
                @else
                    {{ $slot ?? '' }}
                @endif
            </main>
        </div>

        <!-- Loading Management Script -->
        <script>
            class LoadingManager {
                constructor() {
                    this.loadingOverlay = document.getElementById('loadingOverlay');
                    this.loadingProgress = document.getElementById('loadingProgress');
                    this.mainContent = document.getElementById('mainContent');
                    this.progress = 0;
                    this.loaded = false;
                    
                    this.startLoading();
                }
                
                startLoading() {
                    // Simulate progressive loading
                    this.updateProgress(20); // Initial load
                    
                    // Check for DOM content loaded
                    if (document.readyState === 'loading') {
                        document.addEventListener('DOMContentLoaded', () => {
                            this.updateProgress(50);
                        });
                    } else {
                        this.updateProgress(50);
                    }
                    
                    // Check for window load (all resources)
                    if (document.readyState === 'complete') {
                        setTimeout(() => this.updateProgress(80), 100);
                        setTimeout(() => this.completeLoading(), 300);
                    } else {
                        window.addEventListener('load', () => {
                            this.updateProgress(80);
                            setTimeout(() => this.completeLoading(), 300);
                        });
                    }
                    
                    // Fallback: Force completion after 5 seconds
                    setTimeout(() => {
                        if (!this.loaded) {
                            this.completeLoading();
                        }
                    }, 5000);
                }
                
                updateProgress(targetProgress) {
                    if (this.loaded) return;
                    
                    const step = () => {
                        if (this.progress < targetProgress) {
                            this.progress += 2;
                            this.loadingProgress.style.width = this.progress + '%';
                            requestAnimationFrame(step);
                        }
                    };
                    step();
                }
                
                completeLoading() {
                    if (this.loaded) return;
                    this.loaded = true;
                    
                    // Complete progress bar
                    this.updateProgress(100);
                    
                    setTimeout(() => {
                        // Fade out loading overlay
                        this.loadingOverlay.style.opacity = '0';
                        this.loadingOverlay.style.transition = 'opacity 0.5s ease-out';
                        
                        // Fade in main content
                        this.mainContent.style.opacity = '1';
                        
                        // Remove loading overlay after animation
                        setTimeout(() => {
                            this.loadingOverlay.style.display = 'none';
                        }, 500);
                    }, 300);
                }
                
                // Public method to manually complete loading (for AJAX content)
                forceComplete() {
                    this.completeLoading();
                }
            }
            
            // Initialize loading manager
            const loadingManager = new LoadingManager();
            
            // Expose globally for manual control
            window.loadingManager = loadingManager;
        </script>
    </body>
</html>
