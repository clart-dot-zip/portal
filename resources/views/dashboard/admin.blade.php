@section('title', 'Admin Dashboard - ' . config('app.name'))

<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Admin Dashboard') }}
            </h2>
            <div class="flex items-center space-x-4">
                <a href="{{ route('dashboard') }}" 
                   class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200 inline-flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    User View
                </a>
                <div class="text-sm text-gray-500">
                    Last updated: {{ now()->format('M d, Y \a\t g:i A') }}
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Welcome Banner -->
            <div class="welcome-banner overflow-hidden shadow-lg sm:rounded-lg dashboard-card">
                <div class="p-8 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-2xl font-bold mb-2">
                                Welcome back, {{ auth()->user()->name ?? auth()->user()->username }}!
                            </h3>
                            <p class="text-blue-100">
                                Here's an overview of your Authentik instance
                            </p>
                        </div>
                        <div class="hidden md:block">
                            <svg class="w-24 h-24 text-blue-200" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zm0 4a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zm8 0a1 1 0 011-1h4a1 1 0 011 1v6a1 1 0 01-1 1h-4a1 1 0 01-1-1v-6z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
                <!-- Total Users -->
                <div class="bg-white overflow-hidden shadow-lg rounded-lg dashboard-card">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-blue-500 rounded-lg flex items-center justify-center">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-7a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4 flex-1">
                                <p class="text-sm font-medium text-gray-500 truncate">Total Users</p>
                                <p class="text-2xl font-semibold text-gray-900 stat-number">{{ number_format($stats['users']['total']) }}</p>
                            </div>
                        </div>
                        <div class="mt-4">
                            <div class="flex items-center text-sm">
                                <span class="text-green-600 font-medium status-indicator status-active">{{ $stats['users']['active'] }}</span>
                                <span class="text-gray-500 ml-1">active</span>
                                @if($stats['users']['inactive'] > 0)
                                    <span class="text-gray-400 ml-2">•</span>
                                    <span class="text-red-600 font-medium ml-2 status-indicator status-inactive">{{ $stats['users']['inactive'] }}</span>
                                    <span class="text-gray-500 ml-1">inactive</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Groups -->
                <div class="bg-white overflow-hidden shadow-lg rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-green-500 rounded-lg flex items-center justify-center">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4 flex-1">
                                <p class="text-sm font-medium text-gray-500 truncate">Total Groups</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ number_format($stats['groups']['total']) }}</p>
                            </div>
                        </div>
                        <div class="mt-4">
                            <div class="flex items-center text-sm">
                                @if($stats['groups']['superuser_groups'] > 0)
                                    <span class="text-purple-600 font-medium">{{ $stats['groups']['superuser_groups'] }}</span>
                                    <span class="text-gray-500 ml-1">admin groups</span>
                                @endif
                                @if($stats['groups']['empty_groups'] > 0)
                                    <span class="text-gray-400 ml-2">•</span>
                                    <span class="text-orange-600 font-medium ml-2">{{ $stats['groups']['empty_groups'] }}</span>
                                    <span class="text-gray-500 ml-1">empty</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Logins -->
                <div class="bg-white overflow-hidden shadow-lg rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-purple-500 rounded-lg flex items-center justify-center">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4 flex-1">
                                <p class="text-sm font-medium text-gray-500 truncate">Recent Logins</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ number_format($stats['users']['recent_logins']) }}</p>
                            </div>
                        </div>
                        <div class="mt-4">
                            <div class="flex items-center text-sm text-gray-500">
                                <span>Last 7 days</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Applications -->
                <div class="bg-white overflow-hidden shadow-lg rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-indigo-500 rounded-lg flex items-center justify-center">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4 flex-1">
                                <p class="text-sm font-medium text-gray-500 truncate">Applications</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ number_format($stats['applications']['total']) }}</p>
                            </div>
                        </div>
                        <div class="mt-4">
                            <div class="flex items-center text-sm text-gray-500">
                                <span>Connected services</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- System Status -->
                <div class="bg-white overflow-hidden shadow-lg rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                @if($stats['system']['authentik_status'] === 'connected')
                                    <div class="w-12 h-12 bg-green-500 rounded-lg flex items-center justify-center">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </div>
                                @elseif($stats['system']['authentik_status'] === 'error')
                                    <div class="w-12 h-12 bg-red-500 rounded-lg flex items-center justify-center">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                        </svg>
                                    </div>
                                @else
                                    <div class="w-12 h-12 bg-gray-500 rounded-lg flex items-center justify-center">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            <div class="ml-4 flex-1">
                                <p class="text-sm font-medium text-gray-500 truncate">System Status</p>
                                <p class="text-sm font-semibold 
                                    @if($stats['system']['authentik_status'] === 'connected') text-green-600
                                    @elseif($stats['system']['authentik_status'] === 'error') text-red-600
                                    @else text-gray-600 @endif">
                                    {{ ucfirst($stats['system']['authentik_status']) }}
                                </p>
                            </div>
                        </div>
                        <div class="mt-4">
                            <div class="flex items-center text-sm text-gray-500">
                                @if($stats['system']['api_response_time'] > 0)
                                    <span>{{ $stats['system']['api_response_time'] }}ms response</span>
                                @else
                                    <span>No connection</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- User Activity Chart -->
                <div class="bg-white overflow-hidden shadow-lg sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">User Activity</h3>
                        <div class="relative h-64">
                            <canvas id="userActivityChart" class="w-full h-full"></canvas>
                            <div id="userActivityFallback" class="hidden absolute inset-0 flex items-center justify-center">
                                <div class="text-center">
                                    <div class="text-sm text-gray-500 mb-2">User Activity</div>
                                    <div class="text-2xl font-bold text-green-600">{{ $stats['users']['active'] }}</div>
                                    <div class="text-xs text-gray-400">Active Users</div>
                                    @if($stats['users']['inactive'] > 0)
                                        <div class="text-lg font-semibold text-red-600 mt-2">{{ $stats['users']['inactive'] }}</div>
                                        <div class="text-xs text-gray-400">Inactive Users</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Group Membership Chart -->
                <div class="bg-white overflow-hidden shadow-lg sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Group Membership</h3>
                        <div class="relative h-64">
                            <canvas id="groupMembershipChart" class="w-full h-full"></canvas>
                            <div id="groupMembershipFallback" class="hidden absolute inset-0 flex items-center justify-center">
                                <div class="text-center">
                                    <div class="text-sm text-gray-500 mb-4">Top Groups</div>
                                    @foreach(array_slice($chartData['group_membership'], 0, 3) as $group)
                                        <div class="mb-2">
                                            <div class="font-medium text-gray-900">{{ $group['name'] }}</div>
                                            <div class="text-sm text-gray-500">{{ $group['members'] }} members</div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="bg-white overflow-hidden shadow-lg sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Quick Actions</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <a href="{{ route('users.index') }}" class="group relative rounded-lg p-6 bg-white hover:bg-gray-50 border border-gray-200 hover:border-gray-300 transition-colors quick-action-card">
                            <div>
                                <span class="rounded-lg inline-flex p-3 bg-blue-50 text-blue-700 ring-4 ring-white">
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-7a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                                    </svg>
                                </span>
                            </div>
                            <div class="mt-4">
                                <h3 class="text-lg font-medium text-gray-900 group-hover:text-blue-600">
                                    Manage Users
                                    <span class="absolute inset-0" aria-hidden="true"></span>
                                </h3>
                                <p class="mt-2 text-sm text-gray-500">
                                    View, search, and manage user accounts
                                </p>
                            </div>
                        </a>

                        <a href="{{ route('groups.index') }}" class="group relative rounded-lg p-6 bg-white hover:bg-gray-50 border border-gray-200 hover:border-gray-300 transition-colors quick-action-card">
                            <div>
                                <span class="rounded-lg inline-flex p-3 bg-green-50 text-green-700 ring-4 ring-white">
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                </span>
                            </div>
                            <div class="mt-4">
                                <h3 class="text-lg font-medium text-gray-900 group-hover:text-green-600">
                                    Manage Groups
                                    <span class="absolute inset-0" aria-hidden="true"></span>
                                </h3>
                                <p class="mt-2 text-sm text-gray-500">
                                    Configure groups and permissions
                                </p>
                            </div>
                        </a>

                        <div class="group relative rounded-lg p-6 bg-white hover:bg-gray-50 border border-gray-200 hover:border-gray-300 transition-colors cursor-pointer quick-action-card" onclick="syncData()">
                            <div>
                                <span class="rounded-lg inline-flex p-3 bg-purple-50 text-purple-700 ring-4 ring-white">
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                    </svg>
                                </span>
                            </div>
                            <div class="mt-4">
                                <h3 class="text-lg font-medium text-gray-900 group-hover:text-purple-600">
                                    Sync Data
                                </h3>
                                <p class="mt-2 text-sm text-gray-500">
                                    Refresh users and groups from Authentik
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart.js Library -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <script>
        let chartsInitialized = false; // Flag to prevent double initialization
        
        document.addEventListener('DOMContentLoaded', function() {
            // Add a timeout to show fallback if Chart.js takes too long to load
            setTimeout(function() {
                if (typeof Chart === 'undefined') {
                    console.error('Chart.js failed to load, showing fallback content');
                    showFallbackContent();
                    return;
                }
                
                if (!chartsInitialized) {
                    initializeCharts();
                }
            }, 2000); // Wait 2 seconds for Chart.js to load
            
            // Also try to initialize immediately if Chart.js is already available
            if (typeof Chart !== 'undefined' && !chartsInitialized) {
                initializeCharts();
            }
        });
        
        function showFallbackContent() {
            const userCanvas = document.getElementById('userActivityChart');
            const groupCanvas = document.getElementById('groupMembershipChart');
            
            if (userCanvas) {
                userCanvas.style.display = 'none';
                document.getElementById('userActivityFallback').classList.remove('hidden');
            }
            
            if (groupCanvas) {
                groupCanvas.style.display = 'none';
                document.getElementById('groupMembershipFallback').classList.remove('hidden');
            }
        }
        
        function initializeCharts() {
            if (chartsInitialized) {
                console.log('Charts already initialized, skipping...');
                return;
            }
            
            // Mark as initialized to prevent double initialization
            chartsInitialized = true;
            
            // Debug: Check if Chart.js is loaded
            if (typeof Chart === 'undefined') {
                console.error('Chart.js is not loaded!');
                showFallbackContent();
                return;
            }
            
            console.log('Initializing charts...');
            
            // Debug: Log the data being passed
            console.log('Stats data:', @json($stats));
            console.log('Chart data:', @json($chartData));
            
            // User Activity Pie Chart
            const userActivityCanvas = document.getElementById('userActivityChart');
            if (userActivityCanvas) {
                try {
                    const userActivityCtx = userActivityCanvas.getContext('2d');
                    const userActivityData = [{{ $stats['users']['active'] }}, {{ $stats['users']['inactive'] }}];
                    
                    console.log('User activity data:', userActivityData);
                    
                    new Chart(userActivityCtx, {
                        type: 'doughnut',
                        data: {
                            labels: ['Active', 'Inactive'],
                            datasets: [{
                                data: userActivityData,
                                backgroundColor: [
                                    '#10B981', // Green for active
                                    '#EF4444'  // Red for inactive
                                ],
                                borderWidth: 2,
                                borderColor: '#ffffff'
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: {
                                        padding: 20,
                                        usePointStyle: true
                                    }
                                }
                            },
                            cutout: '60%'
                        }
                    });
                    
                    console.log('User activity chart created successfully');
                } catch (error) {
                    console.error('Error creating user activity chart:', error);
                    userActivityCanvas.style.display = 'none';
                    document.getElementById('userActivityFallback').classList.remove('hidden');
                }
            } else {
                console.error('User activity chart canvas not found!');
            }

            // Group Membership Bar Chart
            const groupMembershipCanvas = document.getElementById('groupMembershipChart');
            if (groupMembershipCanvas) {
                try {
                    const groupMembershipCtx = groupMembershipCanvas.getContext('2d');
                    const groupData = @json($chartData['group_membership']);
                    
                    console.log('Group membership data:', groupData);
                    
                    if (!groupData || groupData.length === 0) {
                        groupMembershipCanvas.style.display = 'none';
                        document.getElementById('groupMembershipFallback').classList.remove('hidden');
                        return;
                    }
                    
                    new Chart(groupMembershipCtx, {
                        type: 'bar',
                        data: {
                            labels: groupData.map(item => item.name.length > 15 ? item.name.substring(0, 15) + '...' : item.name),
                            datasets: [{
                                label: 'Members',
                                data: groupData.map(item => item.members),
                                backgroundColor: '#3B82F6',
                                borderColor: '#2563EB',
                                borderWidth: 1,
                                borderRadius: 4
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: false
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        stepSize: 1
                                    }
                                },
                                x: {
                                    ticks: {
                                        maxRotation: 45,
                                        minRotation: 0
                                    }
                                }
                            }
                        }
                    });
                    
                    console.log('Group membership chart created successfully');
                } catch (error) {
                    console.error('Error creating group membership chart:', error);
                    groupMembershipCanvas.style.display = 'none';
                    document.getElementById('groupMembershipFallback').classList.remove('hidden');
                }
            } else {
                console.error('Group membership chart canvas not found!');
            }
        }

        // Sync function
        function syncData() {
            // Show loading state
            const syncButton = event.currentTarget;
            const originalContent = syncButton.innerHTML;
            syncButton.innerHTML = '<div class="animate-spin rounded-full h-8 w-8 border-b-2 border-purple-700 mx-auto"></div>';
            
            // Sync users and groups
            Promise.all([
                fetch('{{ route("users.sync") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                }),
                fetch('{{ route("groups.sync") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                })
            ])
            .then(responses => Promise.all(responses.map(r => r.json())))
            .then(results => {
                // Restore button content
                syncButton.innerHTML = originalContent;
                
                // Show success message
                showMessage('Data synced successfully!', 'success');
                
                // Refresh page after a moment
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            })
            .catch(error => {
                // Restore button content
                syncButton.innerHTML = originalContent;
                showMessage('Sync failed: ' + error.message, 'error');
            });
        }

        function showMessage(message, type) {
            // Create message element
            const messageDiv = document.createElement('div');
            messageDiv.className = `fixed top-4 right-4 z-50 px-6 py-3 rounded-lg shadow-lg ${type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'}`;
            messageDiv.innerHTML = message;

            // Add to page
            document.body.appendChild(messageDiv);

            // Auto-remove after 3 seconds
            setTimeout(() => {
                messageDiv.remove();
            }, 3000);
        }
    </script>
</x-app-layout>