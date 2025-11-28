@section('title', 'Dashboard - ' . config('app.name'))
@section('page_title', 'Dashboard')

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
            <div>
                <h1 class="text-xl font-semibold text-fluent-neutral-30">{{ __('Dashboard') }}</h1>
                <p class="text-sm text-fluent-neutral-26 mt-1">{{ __('Overview of your Authentik environment') }}</p>
            </div>
            <div class="flex items-center gap-2 text-xs text-fluent-neutral-26">
                <svg width="14" height="14" viewBox="0 0 14 14" fill="currentColor">
                    <path fill-rule="evenodd" d="M7 14A7 7 0 107 0a7 7 0 000 14zm0-2A5 5 0 117 2a5 5 0 010 10zm.5-7.5v3.793l2.146 2.147a.5.5 0 01-.707.707l-2.5-2.5A.5.5 0 016 8V4.5a.5.5 0 011 0z" clip-rule="evenodd"/>
                </svg>
                <span>{{ __('Last updated:') }} {{ now()->format('M d, Y \a\t g:i A') }}</span>
            </div>
        </div>
    </x-slot>

    {{-- Statistics Cards Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        {{-- Total Users Card --}}
        <div class="fluent-stat-card fluent-fade-in-up">
            <div class="fluent-stat-icon brand">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 12a5 5 0 100-10 5 5 0 000 10zm0 2c-5.33 0-8 2.67-8 8h16c0-5.33-2.67-8-8-8z"/>
                </svg>
            </div>
            <div class="fluent-stat-value">{{ number_format($stats['users']['total']) }}</div>
            <div class="fluent-stat-label">Total Users</div>
            <a href="{{ route('users.index') }}" class="text-xs text-fluent-brand-60 hover:text-fluent-brand-70 font-semibold flex items-center gap-1 mt-2 transition-colors">
                <span>Manage users</span>
                <svg width="12" height="12" viewBox="0 0 12 12" fill="currentColor">
                    <path d="M4.5 3L7.5 6L4.5 9"/>
                </svg>
            </a>
        </div>

        {{-- Active/Inactive Users Card --}}
        <div class="fluent-stat-card fluent-fade-in-up" style="animation-delay: 0.1s;">
            <div class="fluent-stat-icon success">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 2a10 10 0 110 20 10 10 0 010-20zm4.707 6.293a1 1 0 00-1.414 0L11 12.586l-2.293-2.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l5-5a1 1 0 000-1.414z"/>
                </svg>
            </div>
            <div class="fluent-stat-value">
                {{ number_format($stats['users']['active']) }}
                <span class="text-lg text-fluent-neutral-26 font-normal">/{{ number_format($stats['users']['inactive']) }}</span>
            </div>
            <div class="fluent-stat-label">Active / Inactive Users</div>
            <div class="text-xs text-fluent-neutral-26 mt-2">Tracked in Authentik</div>
        </div>

        {{-- Applications Card --}}
        <div class="fluent-stat-card fluent-fade-in-up" style="animation-delay: 0.2s;">
            <div class="fluent-stat-icon" style="background: linear-gradient(135deg, #ffc83d 0%, #ca7f00 100%);">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M3 5a2 2 0 012-2h14a2 2 0 012 2v3a2 2 0 01-2 2H5a2 2 0 01-2-2V5zm0 9a2 2 0 012-2h6a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5zm11 0a2 2 0 012-2h3a2 2 0 012 2v5a2 2 0 01-2 2h-3a2 2 0 01-2-2v-5z"/>
                </svg>
            </div>
            <div class="fluent-stat-value">{{ number_format($stats['applications']['total']) }}</div>
            <div class="fluent-stat-label">Applications</div>
            <a href="{{ route('applications.index') }}" class="text-xs text-fluent-brand-60 hover:text-fluent-brand-70 font-semibold flex items-center gap-1 mt-2 transition-colors">
                <span>View catalogue</span>
                <svg width="12" height="12" viewBox="0 0 12 12" fill="currentColor">
                    <path d="M4.5 3L7.5 6L4.5 9"/>
                </svg>
            </a>
        </div>

        {{-- System Status Card --}}
        <div class="fluent-stat-card fluent-fade-in-up" style="animation-delay: 0.3s;">
            <div class="fluent-stat-icon {{ $stats['system']['authentik_status'] === 'online' ? 'success' : 'error' }}">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M3 3h8v8H3V3zm10 0h8v8h-8V3zM3 13h8v8H3v-8zm13.5-2a6.5 6.5 0 110 13 6.5 6.5 0 010-13z"/>
                </svg>
            </div>
            <div class="fluent-stat-value capitalize text-lg">{{ $stats['system']['authentik_status'] }}</div>
            <div class="fluent-stat-label">System Status</div>
            <div class="text-xs text-fluent-neutral-26 mt-2">
                {{ $stats['system']['api_response_time'] > 0 ? $stats['system']['api_response_time'].' ms API response' : 'No connection' }}
            </div>
        </div>
    </div>

    {{-- Info Boxes Row --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="fluent-info-box">
            <div class="fluent-info-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 12a5 5 0 100-10 5 5 0 000 10zm1.5 1h-3c-3.33 0-5 1.67-5 5h13c0-3.33-1.67-5-5-5zm6-7a1 1 0 011 1v1.5a.5.5 0 001 0V7a2 2 0 00-2-2h-1.5a.5.5 0 000 1h1.5z"/>
                </svg>
            </div>
            <div class="fluent-info-content">
                <div class="fluent-info-label">Portal Admins</div>
                <div class="fluent-info-value">{{ number_format($stats['users']['portal_admins'] ?? 0) }}</div>
            </div>
        </div>

        <div class="fluent-info-box">
            <div class="fluent-info-icon" style="background: #e0d4fc; color: #8b5cf6;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M11 7h2v7h-2V7zm0 8h2v2h-2v-2z"/>
                    <path fill-rule="evenodd" d="M12 2a10 10 0 100 20 10 10 0 000-20zM4 12a8 8 0 1116 0 8 8 0 01-16 0z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="fluent-info-content">
                <div class="fluent-info-label">Recent Logins (7d)</div>
                <div class="fluent-info-value">{{ number_format($stats['users']['recent_logins']) }}</div>
            </div>
        </div>

        <div class="fluent-info-box">
            <div class="fluent-info-icon" style="background: #dff6dd; color: #107c10;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M3 5a2 2 0 012-2h6a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2V5zm0 10a2 2 0 012-2h6a2 2 0 012 2v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4zm12-10a2 2 0 012-2h4a2 2 0 012 2v4a2 2 0 01-2 2h-4a2 2 0 01-2-2V5zm0 10a2 2 0 012-2h4a2 2 0 012 2v4a2 2 0 01-2 2h-4a2 2 0 01-2-2v-4z"/>
                </svg>
            </div>
            <div class="fluent-info-content">
                <div class="fluent-info-label">Groups w/ Members</div>
                <div class="fluent-info-value">{{ number_format($stats['groups']['total'] - $stats['groups']['empty_groups']) }}</div>
            </div>
        </div>
    </div>

    {{-- Charts Row --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-6">
        {{-- User Activity Chart --}}
        <div class="fluent-chart-container">
            <div class="fluent-chart-header">
                <h3 class="fluent-chart-title">User Activity</h3>
                <x-fluent-badge variant="success" size="small">Real-time</x-fluent-badge>
            </div>
            <div class="fluent-chart-canvas">
                <canvas id="userActivityChart"></canvas>
                <div id="userActivityFallback" class="absolute inset-0 flex flex-col items-center justify-center text-center" style="display:none;">
                    <p class="text-sm text-fluent-neutral-26 mb-2">User Activity</p>
                    <div class="text-4xl font-semibold text-fluent-success mb-1">{{ $stats['users']['active'] }}</div>
                    <p class="text-xs text-fluent-neutral-26">Active Users</p>
                    @if($stats['users']['inactive'] > 0)
                        <div class="text-2xl font-semibold text-fluent-error mt-3">{{ $stats['users']['inactive'] }}</div>
                        <p class="text-xs text-fluent-neutral-26">Inactive Users</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Group Membership Chart --}}
        <div class="fluent-chart-container">
            <div class="fluent-chart-header">
                <h3 class="fluent-chart-title">Group Membership</h3>
                <x-fluent-badge variant="info" size="small">Top Groups</x-fluent-badge>
            </div>
            <div class="fluent-chart-canvas">
                <canvas id="groupMembershipChart"></canvas>
                <div id="groupMembershipFallback" class="absolute inset-0 flex items-center justify-center" style="display:none;">
                    <div class="text-center">
                        <p class="text-sm text-fluent-neutral-26 mb-4">Group Data</p>
                        @foreach(array_slice($chartData['group_membership'], 0, 3) as $group)
                            <div class="mb-3">
                                <p class="font-semibold text-fluent-neutral-30 text-sm">{{ $group['name'] }}</p>
                                <p class="text-xs text-fluent-neutral-26">{{ $group['members'] }} members</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Actions and Status Row --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        {{-- Quick Actions --}}
        <div class="lg:col-span-2">
            <x-fluent-card title="Quick Actions" padding="normal">
                <x-slot name="header">
                    <div class="flex items-center justify-between">
                        <h3 class="text-base font-semibold text-fluent-neutral-30">Quick Actions</h3>
                        <x-fluent-badge variant="brand" size="small">Admin</x-fluent-badge>
                    </div>
                </x-slot>

                <div class="fluent-quick-actions">
                    <a href="{{ route('users.index') }}" class="fluent-action-tile">
                        <div class="fluent-action-icon">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zm2 0a3 3 0 116 0 3 3 0 01-6 0zM5 11a5 5 0 015 5v1H0v-1a5 5 0 015-5zm10 0a5 5 0 015 5v1h-10v-1a5 5 0 015-5z"/>
                            </svg>
                        </div>
                        <span class="fluent-action-label">Manage Users</span>
                    </a>

                    <a href="{{ route('groups.index') }}" class="fluent-action-tile">
                        <div class="fluent-action-icon">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M3 3a2 2 0 012-2h6a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2V3zm0 10a2 2 0 012-2h6a2 2 0 012 2v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4zm12-10a2 2 0 012-2h4a2 2 0 012 2v4a2 2 0 01-2 2h-4a2 2 0 01-2-2V3zm0 10a2 2 0 012-2h4a2 2 0 012 2v4a2 2 0 01-2 2h-4a2 2 0 01-2-2v-4z"/>
                            </svg>
                        </div>
                        <span class="fluent-action-label">Manage Groups</span>
                    </a>

                    <button type="button" class="fluent-action-tile" onclick="syncData()">
                        <div class="fluent-action-icon">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <span class="fluent-action-label">Sync Directory</span>
                    </button>

                    <a href="{{ route('applications.index') }}" class="fluent-action-tile">
                        <div class="fluent-action-icon">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zm10 0a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/>
                            </svg>
                        </div>
                        <span class="fluent-action-label">Applications</span>
                    </a>

                    <a href="{{ route('git-management.index') }}" class="fluent-action-tile">
                        <div class="fluent-action-icon">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M12.316 3.051a1 1 0 01.633 1.265l-4 12a1 1 0 11-1.898-.632l4-12a1 1 0 011.265-.633zM5.707 6.293a1 1 0 010 1.414L3.414 10l2.293 2.293a1 1 0 11-1.414 1.414l-3-3a1 1 0 010-1.414l3-3a1 1 0 011.414 0zm8.586 0a1 1 0 011.414 0l3 3a1 1 0 010 1.414l-3 3a1 1 0 11-1.414-1.414L16.586 10l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <span class="fluent-action-label">Git Management</span>
                    </a>

                    <a href="{{ route('pim.index') }}" class="fluent-action-tile">
                        <div class="fluent-action-icon">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 2a1 1 0 00-1 1v1a1 1 0 002 0V3a1 1 0 00-1-1zM4 4h3a3 3 0 006 0h3a2 2 0 012 2v9a2 2 0 01-2 2H4a2 2 0 01-2-2V6a2 2 0 012-2zm2.5 7a1.5 1.5 0 100-3 1.5 1.5 0 000 3zm2.45 4a2.5 2.5 0 10-4.9 0h4.9zM12 9a1 1 0 100 2h3a1 1 0 100-2h-3zm-1 4a1 1 0 011-1h2a1 1 0 110 2h-2a1 1 0 01-1-1z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <span class="fluent-action-label">PIM Sessions</span>
                    </a>
                </div>
            </x-fluent-card>
        </div>

        {{-- Status & Sync Panel --}}
        <div>
            <x-fluent-card padding="normal">
                <x-slot name="header">
                    <h3 class="text-base font-semibold text-fluent-neutral-30">Status & Sync</h3>
                </x-slot>

                <p class="text-sm text-fluent-neutral-26 mb-4">Stay current with Authentik data.</p>
                
                <ul class="space-y-3 mb-6">
                    <li class="flex items-start gap-2 text-sm">
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor" class="text-fluent-success flex-shrink-0 mt-0.5">
                            <path fill-rule="evenodd" d="M13.854 3.646a.5.5 0 010 .708l-7 7a.5.5 0 01-.708 0l-3.5-3.5a.5.5 0 11.708-.708L6.5 10.293l6.646-6.647a.5.5 0 01.708 0z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-fluent-neutral-30">User and group cache overview</span>
                    </li>
                    <li class="flex items-start gap-2 text-sm">
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor" class="text-fluent-brand-60 flex-shrink-0 mt-0.5">
                            <path d="M7 3a2 2 0 00-2 2v8a2 2 0 002 2h6a2 2 0 002-2V5a2 2 0 00-2-2H7zM3 5a4 4 0 014-4h6a4 4 0 014 4v8a4 4 0 01-4 4H7a4 4 0 01-4-4V5z"/>
                        </svg>
                        <span class="text-fluent-neutral-30">Sync logs recorded automatically</span>
                    </li>
                    <li class="flex items-start gap-2 text-sm">
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor" class="text-fluent-warning flex-shrink-0 mt-0.5" style="color: #ca7f00;">
                            <path fill-rule="evenodd" d="M8 1a7 7 0 110 14A7 7 0 018 1zm0 2a5 5 0 100 10A5 5 0 008 3zm.5 1.5v3.793l2.146 2.147a.5.5 0 01-.707.707l-2.5-2.5A.5.5 0 017 8V4.5a.5.5 0 011 0z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-fluent-neutral-30">Last sync {{ $stats['system']['last_sync'] ?? 'Not recorded' }}</span>
                    </li>
                </ul>

                <x-fluent-button variant="primary" class="w-full" onclick="syncData()">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                        <path fill-rule="evenodd" d="M3 2a1 1 0 011 1v1.586a6 6 0 019.201 2.414 1 1 0 11-1.885.666 4 4 0 00-6.316-1.1V9a1 1 0 01-2 0V3a1 1 0 011-1zm.006 7.046a1 1 0 011.276.61 4 4 0 006.316 1.1V7a1 1 0 112 0v6a1 1 0 01-1 1h-.058a6 6 0 01-9.143-2.414 1 1 0 01.61-1.276z" clip-rule="evenodd"/>
                    </svg>
                    Sync Users & Groups
                </x-fluent-button>
            </x-fluent-card>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        let chartsInitialized = false;
        const portalStats = @json($stats);
        const portalGroupMembership = @json($chartData['group_membership'] ?? []);

        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                if (typeof Chart === 'undefined') {
                    showFallbackContent();
                    return;
                }

                if (!chartsInitialized) {
                    initializeCharts();
                }
            }, 1000);

            if (typeof Chart !== 'undefined' && !chartsInitialized) {
                initializeCharts();
            }
        });

        function showFallbackContent() {
            const userCanvas = document.getElementById('userActivityChart');
            const groupCanvas = document.getElementById('groupMembershipChart');

            if (userCanvas) {
                userCanvas.style.display = 'none';
                document.getElementById('userActivityFallback').style.display = 'flex';
            }

            if (groupCanvas) {
                groupCanvas.style.display = 'none';
                document.getElementById('groupMembershipFallback').style.display = 'flex';
            }
        }

        function initializeCharts() {
            if (chartsInitialized) return;
            chartsInitialized = true;

            if (typeof Chart === 'undefined') {
                showFallbackContent();
                return;
            }

            // Fluent UI Chart Colors
            const fluentColors = {
                brand: '#0078d4',
                success: '#107c10',
                error: '#d13438',
                warning: '#ca7f00',
                neutral: '#605e5c',
            };

            // User Activity Chart (Doughnut)
            const userActivityCanvas = document.getElementById('userActivityChart');
            if (userActivityCanvas) {
                try {
                    const userActivityCtx = userActivityCanvas.getContext('2d');
                    new Chart(userActivityCtx, {
                        type: 'doughnut',
                        data: {
                            labels: ['Active', 'Inactive'],
                            datasets: [{
                                data: [portalStats.users.active, portalStats.users.inactive],
                                backgroundColor: [fluentColors.success, fluentColors.error],
                                borderWidth: 0,
                                borderRadius: 4,
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            cutout: '70%',
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: {
                                        padding: 15,
                                        font: {
                                            family: 'Segoe UI, system-ui, sans-serif',
                                            size: 12,
                                        },
                                        usePointStyle: true,
                                        pointStyle: 'circle',
                                    }
                                },
                                tooltip: {
                                    backgroundColor: 'rgba(255, 255, 255, 0.95)',
                                    titleColor: '#323130',
                                    bodyColor: '#605e5c',
                                    borderColor: '#edebe9',
                                    borderWidth: 1,
                                    padding: 12,
                                    cornerRadius: 8,
                                    titleFont: {
                                        family: 'Segoe UI, system-ui, sans-serif',
                                        size: 14,
                                        weight: '600',
                                    },
                                    bodyFont: {
                                        family: 'Segoe UI, system-ui, sans-serif',
                                        size: 13,
                                    }
                                }
                            }
                        }
                    });
                } catch (error) {
                    console.error('Error creating user activity chart:', error);
                    userActivityCanvas.style.display = 'none';
                    document.getElementById('userActivityFallback').style.display = 'flex';
                }
            }

            // Group Membership Chart (Bar)
            const groupMembershipCanvas = document.getElementById('groupMembershipChart');
            if (groupMembershipCanvas) {
                try {
                    if (!portalGroupMembership || portalGroupMembership.length === 0) {
                        groupMembershipCanvas.style.display = 'none';
                        document.getElementById('groupMembershipFallback').style.display = 'flex';
                        return;
                    }

                    const groupMembershipCtx = groupMembershipCanvas.getContext('2d');
                    new Chart(groupMembershipCtx, {
                        type: 'bar',
                        data: {
                            labels: portalGroupMembership.map(item => 
                                item.name.length > 18 ? item.name.substring(0, 18) + '…' : item.name
                            ),
                            datasets: [{
                                label: 'Members',
                                data: portalGroupMembership.map(item => item.members),
                                backgroundColor: fluentColors.brand,
                                borderRadius: 4,
                                borderSkipped: false,
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    backgroundColor: 'rgba(255, 255, 255, 0.95)',
                                    titleColor: '#323130',
                                    bodyColor: '#605e5c',
                                    borderColor: '#edebe9',
                                    borderWidth: 1,
                                    padding: 12,
                                    cornerRadius: 8,
                                    titleFont: {
                                        family: 'Segoe UI, system-ui, sans-serif',
                                        size: 14,
                                        weight: '600',
                                    },
                                    bodyFont: {
                                        family: 'Segoe UI, system-ui, sans-serif',
                                        size: 13,
                                    }
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    grid: {
                                        color: '#f3f2f1',
                                        drawBorder: false,
                                    },
                                    ticks: {
                                        font: {
                                            family: 'Segoe UI, system-ui, sans-serif',
                                            size: 11,
                                        },
                                        color: '#605e5c',
                                    }
                                },
                                x: {
                                    grid: {
                                        display: false,
                                        drawBorder: false,
                                    },
                                    ticks: {
                                        maxRotation: 35,
                                        minRotation: 0,
                                        font: {
                                            family: 'Segoe UI, system-ui, sans-serif',
                                            size: 11,
                                        },
                                        color: '#605e5c',
                                    }
                                }
                            }
                        }
                    });
                } catch (error) {
                    console.error('Error creating group membership chart:', error);
                    groupMembershipCanvas.style.display = 'none';
                    document.getElementById('groupMembershipFallback').style.display = 'flex';
                }
            }
        }

        function syncData() {
            const button = event.currentTarget;
            const originalHTML = button.innerHTML;
            button.disabled = true;
            button.innerHTML = '<svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Syncing...';

            window.FluentUI.showLoading('Syncing data with Authentik...');

            Promise.all([
                fetch('{{ route("users.sync") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    }
                }),
                fetch('{{ route("groups.sync") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    }
                })
            ])
            .then(responses => {
                if (!responses.every(response => response.ok)) {
                    throw new Error('Failed to sync data');
                }
                window.FluentUI.hideLoading();
                window.FluentUI.showToast('Data synced successfully!', 'success');
                setTimeout(() => window.location.reload(), 1200);
            })
            .catch(error => {
                console.error('Sync error:', error);
                window.FluentUI.hideLoading();
                window.FluentUI.showToast('Sync failed: ' + error.message, 'error');
                button.disabled = false;
                button.innerHTML = originalHTML;
            });
        }
    </script>
</x-app-layout>