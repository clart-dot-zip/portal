@section('title', 'Dashboard - ' . config('app.name'))

<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Dashboard') }}
            </h2>
            @if($isPortalAdmin)
                <div class="flex space-x-2">
                    <a href="{{ route('admin.dashboard') }}" 
                       class="bg-purple-500 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline inline-flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Admin Dashboard
                    </a>
                </div>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Status Messages -->
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <!-- Welcome Section -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">
                                Welcome back, {{ Auth::user()->name }}!
                            </h3>
                            <p class="mt-1 text-sm text-gray-600">
                                Here's your personal dashboard with account information and available applications.
                            </p>
                        </div>
                        <div class="text-right">
                            @if($isPortalAdmin)
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                    Portal Administrator
                                </span>
                            @else
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                    User
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
                
                <!-- Account Overview -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h4 class="text-lg font-medium text-gray-900">Your Account</h4>
                                <p class="text-sm text-gray-600">Profile and settings</p>
                            </div>
                        </div>
                        <div class="mt-4 space-y-2">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Username:</span>
                                <span class="font-medium">{{ Auth::user()->username }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Email:</span>
                                <span class="font-medium">{{ Auth::user()->email }}</span>
                            </div>
                            @if($userStats['account_created'])
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Member Since:</span>
                                    <span class="font-medium">{{ $userStats['account_created']->format('M Y') }}</span>
                                </div>
                            @endif
                        </div>
                        <div class="mt-4">
                            <a href="{{ route('users.profile') }}" 
                               class="w-full bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline text-center block">
                                Manage Profile
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Groups & Access -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h4 class="text-lg font-medium text-gray-900">Your Groups</h4>
                                <p class="text-sm text-gray-600">Memberships and access</p>
                            </div>
                        </div>
                        <div class="mt-4">
                            <div class="text-center">
                                <span class="text-2xl font-bold text-green-600">{{ $userStats['groups_count'] }}</span>
                                <p class="text-sm text-gray-600">{{ $userStats['groups_count'] === 1 ? 'Group' : 'Groups' }}</p>
                            </div>
                            @if(isset($userStats['groups']) && count($userStats['groups']) > 0)
                                <div class="mt-3 space-y-1">
                                    @foreach(array_slice($userStats['groups'], 0, 3) as $group)
                                        <div class="text-xs bg-gray-100 rounded px-2 py-1">
                                            {{ $group['name'] }}
                                            @if($group['is_superuser'] ?? false)
                                                <span class="ml-1 text-purple-600">★</span>
                                            @endif
                                        </div>
                                    @endforeach
                                    @if(count($userStats['groups']) > 3)
                                        <div class="text-xs text-gray-500">
                                            +{{ count($userStats['groups']) - 3 }} more
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Last Activity -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h4 class="text-lg font-medium text-gray-900">Last Activity</h4>
                                <p class="text-sm text-gray-600">Recent login information</p>
                            </div>
                        </div>
                        <div class="mt-4 text-center">
                            @if($userStats['last_login'])
                                <div class="text-sm text-gray-600">Last login:</div>
                                <div class="text-lg font-medium">{{ $userStats['last_login']->diffForHumans() }}</div>
                                <div class="text-xs text-gray-500 mt-1">{{ $userStats['last_login']->format('M j, Y \a\t g:i A') }}</div>
                            @else
                                <div class="text-lg font-medium text-gray-500">No recent activity</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Available Applications Section -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Your Applications</h3>
                    
                    @if(count($personalApps) > 0)
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($personalApps as $app)
                                <div class="border rounded-lg p-4 hover:shadow-md transition-shadow">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0">
                                            @if($app['icon'])
                                                <img src="{{ $app['icon'] }}" alt="{{ $app['name'] }}" class="h-8 w-8">
                                            @else
                                                <div class="h-8 w-8 bg-gray-300 rounded flex items-center justify-center">
                                                    <span class="text-gray-600 text-xs font-medium">{{ substr($app['name'], 0, 2) }}</span>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="ml-3">
                                            <h4 class="text-sm font-medium text-gray-900">{{ $app['name'] }}</h4>
                                            <p class="text-xs text-gray-500">{{ $app['description'] ?? 'Application' }}</p>
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <a href="{{ $app['launch_url'] }}" 
                                           target="_blank"
                                           class="w-full bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline text-center block text-sm">
                                            Launch
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No applications available</h3>
                            <p class="mt-1 text-sm text-gray-500">
                                Contact your administrator to get access to applications.
                            </p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Quick Actions (if admin) -->
            @if($isPortalAdmin)
                <div class="mt-6 bg-purple-50 border border-purple-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-purple-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 8a6 6 0 01-7.743 5.743L10 14l-0.257-0.257A6 6 0 1118 8zm-1.5 0a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM10 7a1 1 0 100 2 1 1 0 000-2z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-purple-800">
                                Portal Administrator Actions
                            </h3>
                            <div class="mt-2 text-sm text-purple-700">
                                <div class="flex flex-wrap gap-2">
                                    <a href="{{ route('users.index') }}" class="text-purple-600 hover:text-purple-900 underline">Manage Users</a>
                                    <span class="text-purple-400">•</span>
                                    <a href="{{ route('groups.index') }}" class="text-purple-600 hover:text-purple-900 underline">Manage Groups</a>
                                    <span class="text-purple-400">•</span>
                                    <a href="{{ route('applications.index') }}" class="text-purple-600 hover:text-purple-900 underline">Manage Applications</a>
                                    <span class="text-purple-400">•</span>
                                    <a href="{{ route('admin.dashboard') }}" class="text-purple-600 hover:text-purple-900 underline">View Admin Dashboard</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>