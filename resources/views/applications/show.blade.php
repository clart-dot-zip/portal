@section('title', 'Application Details: ' . $application['name'] . ' - ' . config('app.name'))

<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Application Details') }}: {{ $application['name'] }}
            </h2>
            <div class="flex space-x-3">
                <a href="{{ route('applications.edit', $application['pk']) }}" 
                   class="bg-yellow-500 hover:bg-yellow-600 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200 inline-flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    Manage Access
                </a>
                <a href="{{ route('applications.index') }}" 
                   class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200 inline-flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Applications
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Status Messages -->
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <!-- Application Details -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-6">Application Information</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wide">Basic Information</h4>
                            <dl class="mt-3 space-y-3">
                                <div>
                                    <dt class="text-sm font-medium text-gray-900">Name</dt>
                                    <dd class="text-sm text-gray-600">{{ $application['name'] }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-900">Slug</dt>
                                    <dd class="text-sm text-gray-600">{{ $application['slug'] ?? 'Not set' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-900">UUID</dt>
                                    <dd class="text-sm text-gray-600 font-mono">{{ $application['pk'] }}</dd>
                                </div>
                                @if(isset($application['meta_description']) && $application['meta_description'])
                                    <div>
                                        <dt class="text-sm font-medium text-gray-900">Description</dt>
                                        <dd class="text-sm text-gray-600">{{ $application['meta_description'] }}</dd>
                                    </div>
                                @endif
                            </dl>
                        </div>

                        <div>
                            <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wide">Configuration</h4>
                            <dl class="mt-3 space-y-3">
                                <div>
                                    <dt class="text-sm font-medium text-gray-900">Provider</dt>
                                    <dd class="text-sm text-gray-600">
                                        @if(isset($application['provider']) && $application['provider'])
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                {{ $application['provider_obj']['name'] ?? 'Configured' }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                Not configured
                                            </span>
                                        @endif
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-900">Policy Engine Mode</dt>
                                    <dd class="text-sm text-gray-600">{{ ucfirst($application['policy_engine_mode'] ?? 'any') }}</dd>
                                </div>
                                @if(isset($application['meta_launch_url']) && $application['meta_launch_url'])
                                    <div>
                                        <dt class="text-sm font-medium text-gray-900">Launch URL</dt>
                                        <dd class="text-sm text-gray-600">
                                            <a href="{{ $application['meta_launch_url'] }}" 
                                               target="_blank" 
                                               class="text-blue-600 hover:text-blue-800 underline">
                                                {{ $application['meta_launch_url'] }}
                                                <svg class="inline h-4 w-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                                </svg>
                                            </a>
                                        </dd>
                                    </div>
                                @endif
                            </dl>
                        </div>
                    </div>

                    <!-- Application Icon -->
                    @if(isset($application['meta_icon']) && $application['meta_icon'])
                        <div class="mt-6">
                            <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wide mb-3">Application Icon</h4>
                            <img src="{{ $application['meta_icon'] }}" 
                                 alt="{{ $application['name'] }} icon" 
                                 class="h-16 w-16 rounded-lg shadow-sm border border-gray-200">
                        </div>
                    @endif
                </div>
            </div>

            <!-- Application Statistics -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-6">Access Statistics</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Total Users with Access -->
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-7a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-blue-600">Direct User Access</p>
                                    <p class="text-2xl font-semibold text-blue-900" id="userAccessCount">-</p>
                                </div>
                            </div>
                        </div>

                        <!-- Groups with Access -->
                        <div class="bg-green-50 p-4 rounded-lg">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-green-600">Group Access</p>
                                    <p class="text-2xl font-semibold text-green-900" id="groupAccessCount">-</p>
                                </div>
                            </div>
                        </div>

                        <!-- Total Access -->
                        <div class="bg-purple-50 p-4 rounded-lg">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="h-8 w-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-purple-600">Total Policies</p>
                                    <p class="text-2xl font-semibold text-purple-900" id="totalPolicyCount">-</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-6">Quick Actions</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <a href="{{ route('applications.edit', $application['pk']) }}" 
                           class="group relative rounded-lg p-6 bg-white hover:bg-gray-50 border border-gray-200 hover:border-gray-300 transition-colors">
                            <div>
                                <span class="rounded-lg inline-flex p-3 bg-indigo-50 text-indigo-700 ring-4 ring-white">
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                    </svg>
                                </span>
                            </div>
                            <div class="mt-4">
                                <h3 class="text-lg font-medium text-gray-900 group-hover:text-indigo-600">
                                    Manage Access
                                    <span class="absolute inset-0" aria-hidden="true"></span>
                                </h3>
                                <p class="mt-2 text-sm text-gray-500">
                                    Assign users and groups access to this application
                                </p>
                            </div>
                        </a>

                        @if(isset($application['meta_launch_url']) && $application['meta_launch_url'])
                            <a href="{{ $application['meta_launch_url'] }}" 
                               target="_blank"
                               class="group relative rounded-lg p-6 bg-white hover:bg-gray-50 border border-gray-200 hover:border-gray-300 transition-colors">
                                <div>
                                    <span class="rounded-lg inline-flex p-3 bg-blue-50 text-blue-700 ring-4 ring-white">
                                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                        </svg>
                                    </span>
                                </div>
                                <div class="mt-4">
                                    <h3 class="text-lg font-medium text-gray-900 group-hover:text-blue-600">
                                        Launch Application
                                        <span class="absolute inset-0" aria-hidden="true"></span>
                                    </h3>
                                    <p class="mt-2 text-sm text-gray-500">
                                        Open this application in a new tab
                                    </p>
                                </div>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Load access statistics from server data
            loadAccessStatistics();
        });

        function loadAccessStatistics() {
            // Use the policy bindings data passed from the controller
            const policyBindings = {!! json_encode($policyBindings ?? []) !!};
            
            // Count users and groups with access
            let userCount = 0;
            let groupCount = 0;
            
            policyBindings.forEach(binding => {
                if (binding.user) {
                    userCount++;
                }
                if (binding.group) {
                    groupCount++;
                }
            });
            
            document.getElementById('userAccessCount').textContent = userCount;
            document.getElementById('groupAccessCount').textContent = groupCount;
            document.getElementById('totalPolicyCount').textContent = policyBindings.length;
            
            console.log('Policy bindings loaded:', {
                total: policyBindings.length,
                users: userCount,
                groups: groupCount,
                bindings: policyBindings
            });
        }
    </script>
</x-app-layout>