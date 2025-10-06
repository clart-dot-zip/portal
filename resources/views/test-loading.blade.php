@section('title', 'Loading Test - ' . config('app.name'))

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Loading Test Page') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">Loading System Test</h3>
                    
                    <div class="space-y-4">
                        <button onclick="testLoading()" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Test Loading Overlay
                        </button>
                        
                        <button onclick="testButtonLoading(this)" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                            Test Button Loading
                        </button>
                        
                        <div class="mt-6">
                            <h4 class="font-semibold mb-2">Sample Loading Cards:</h4>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="bg-white p-4 border rounded-lg skeleton-card">
                                    <div class="skeleton-text"></div>
                                    <div class="skeleton-text"></div>
                                    <div class="skeleton-text"></div>
                                </div>
                                <div class="bg-white p-4 border rounded-lg">
                                    <div class="loading-spinner mb-2"></div>
                                    <p>Loading content...</p>
                                </div>
                                <div class="bg-white p-4 border rounded-lg loading-pulse">
                                    <p>Pulsing loading card</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function testLoading() {
            if (window.loadingManager) {
                // Show loading overlay
                window.loadingManager.loadingOverlay.style.display = 'flex';
                window.loadingManager.loadingOverlay.style.opacity = '1';
                
                // Simulate progress
                window.loadingManager.progress = 0;
                window.loadingManager.loaded = false;
                window.loadingManager.updateProgress(30);
                
                setTimeout(() => window.loadingManager.updateProgress(60), 1000);
                setTimeout(() => window.loadingManager.updateProgress(90), 2000);
                setTimeout(() => window.loadingManager.forceComplete(), 3000);
            }
        }
        
        function testButtonLoading(button) {
            const originalText = button.textContent;
            button.innerHTML = '<div class="loading-spinner mr-2"></div>Loading...';
            button.disabled = true;
            button.classList.add('loading-button');
            
            setTimeout(() => {
                button.innerHTML = originalText;
                button.disabled = false;
                button.classList.remove('loading-button');
            }, 3000);
        }
    </script>
</x-app-layout>