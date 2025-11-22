@section('title', 'Dashboard - ' . config('app.name'))

<x-app-layout>
    <x-slot name="header">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between">
            <div>
                <h1 class="h3 text-dark mb-1">{{ __('Dashboard') }}</h1>
                <p class="text-muted mb-0">{{ __('Overview of your Authentik environment') }}</p>
            </div>
            <div class="text-muted small mt-3 mt-md-0">
                <i class="far fa-clock mr-1"></i>
                {{ __('Last updated:') }} {{ now()->format('M d, Y \a\t g:i A') }}
            </div>
        </div>
    </x-slot>

    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info adminlte-info-box">
                <div class="inner">
                    <h3>{{ number_format($stats['users']['total']) }}</h3>
                    <p>Total Users</p>
                </div>
                <div class="icon">
                    <i class="fas fa-users"></i>
                </div>
                <a href="{{ route('users.index') }}" class="small-box-footer">
                    Manage users <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success adminlte-info-box">
                <div class="inner">
                    <h3>{{ number_format($stats['users']['active']) }}<sup class="text-sm">/{{ number_format($stats['users']['inactive']) }}</sup></h3>
                    <p>Active / Inactive</p>
                </div>
                <div class="icon">
                    <i class="fas fa-user-check"></i>
                </div>
                <span class="small-box-footer text-white-50">
                    {{ __('Tracked users in Authentik') }}
                </span>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning adminlte-info-box">
                <div class="inner text-white">
                    <h3>{{ number_format($stats['applications']['total']) }}</h3>
                    <p class="text-white">Applications</p>
                </div>
                <div class="icon">
                    <i class="fas fa-th-large"></i>
                </div>
                <a href="{{ route('applications.index') }}" class="small-box-footer text-white">
                    View catalogue <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger adminlte-info-box">
                <div class="inner">
                    <h3 class="text-white text-capitalize">{{ $stats['system']['authentik_status'] }}</h3>
                    <p class="text-white">System Status</p>
                </div>
                <div class="icon">
                    <i class="fas fa-server"></i>
                </div>
                <span class="small-box-footer text-white-50">
                    {{ $stats['system']['api_response_time'] > 0 ? $stats['system']['api_response_time'].' ms API' : 'No connection' }}
                </span>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="info-box">
                <span class="info-box-icon bg-primary"><i class="fas fa-user-shield"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Portal Admins</span>
                    <span class="info-box-number">{{ number_format($stats['users']['portal_admins'] ?? 0) }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="info-box">
                <span class="info-box-icon bg-purple"><i class="fas fa-sign-in-alt"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Recent Logins (7d)</span>
                    <span class="info-box-number">{{ number_format($stats['users']['recent_logins']) }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="info-box">
                <span class="info-box-icon bg-success"><i class="fas fa-layer-group"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Groups w/ Members</span>
                    <span class="info-box-number">{{ number_format($stats['groups']['total'] - $stats['groups']['empty_groups']) }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <div class="card card-primary card-outline h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">User Activity</h3>
                    <span class="badge badge-pill badge-soft-success">Real-time</span>
                </div>
                <div class="card-body">
                    <div class="position-relative" style="min-height: 260px;">
                        <canvas id="userActivityChart"></canvas>
                        <div id="userActivityFallback" class="position-absolute w-100 h-100 d-flex flex-column align-items-center justify-content-center text-center text-muted" style="display:none;">
                            <p class="mb-1">User Activity</p>
                            <div class="display-4 text-success">{{ $stats['users']['active'] }}</div>
                            <small>Active Users</small>
                            @if($stats['users']['inactive'] > 0)
                                <div class="h5 text-danger mt-2">{{ $stats['users']['inactive'] }}</div>
                                <small>Inactive Users</small>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card card-primary card-outline h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Group Membership</h3>
                    <span class="badge badge-pill badge-soft-info">Top Groups</span>
                </div>
                <div class="card-body">
                    <div class="position-relative" style="min-height: 260px;">
                        <canvas id="groupMembershipChart"></canvas>
                        <div id="groupMembershipFallback" class="position-absolute w-100 h-100 d-flex align-items-center justify-content-center" style="display:none;">
                            <div class="text-center text-muted">
                                <p class="mb-3">Group data unavailable</p>
                                @foreach(array_slice($chartData['group_membership'], 0, 3) as $group)
                                    <div class="mb-2">
                                        <strong>{{ $group['name'] }}</strong>
                                        <small class="d-block text-muted">{{ $group['members'] }} members</small>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card card-outline card-primary h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Quick Actions</h3>
                    <span class="badge badge-soft-primary">Admin</span>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <a href="{{ route('users.index') }}" class="btn btn-outline-primary btn-block text-left">
                                <i class="fas fa-users mr-2"></i> Manage Users
                            </a>
                        </div>
                        <div class="col-md-4 mb-3">
                            <a href="{{ route('groups.index') }}" class="btn btn-outline-success btn-block text-left">
                                <i class="fas fa-layer-group mr-2"></i> Manage Groups
                            </a>
                        </div>
                        <div class="col-md-4 mb-3">
                            <button type="button" class="btn btn-outline-warning btn-block text-left" onclick="syncData()">
                                <i class="fas fa-sync mr-2"></i> Sync Directory
                            </button>
                        </div>
                        <div class="col-md-4 mb-3">
                            <a href="{{ route('applications.index') }}" class="btn btn-outline-info btn-block text-left">
                                <i class="fas fa-th mr-2"></i> Applications
                            </a>
                        </div>
                        <div class="col-md-4 mb-3">
                            <a href="{{ route('git-management.index') }}" class="btn btn-outline-secondary btn-block text-left">
                                <i class="fas fa-code-branch mr-2"></i> Git Management
                            </a>
                        </div>
                        <div class="col-md-4 mb-3">
                            <a href="{{ route('pim.index') }}" class="btn btn-outline-dark btn-block text-left">
                                <i class="fas fa-id-card-alt mr-2"></i> PIM Sessions
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card card-outline card-danger h-100">
                <div class="card-header">
                    <h3 class="card-title mb-0">Status & Sync</h3>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3">Stay current with Authentik data.</p>
                    <ul class="list-unstyled mb-4">
                        <li class="mb-2">
                            <i class="fas fa-check text-success mr-2"></i> User and group cache overview
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-database text-info mr-2"></i> Sync logs recorded automatically
                        </li>
                        <li>
                            <i class="fas fa-clock text-warning mr-2"></i> Last sync {{ $stats['system']['last_sync'] ?? 'Not recorded' }}
                        </li>
                    </ul>
                    <button type="button" class="btn btn-portal-primary btn-block" onclick="syncData()">
                        <i class="fas fa-sync mr-2"></i> Sync Users & Groups
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        let chartsInitialized = false;
        const portalStats = <?php echo json_encode($stats); ?>;
        const portalGroupMembership = <?php echo json_encode($chartData['group_membership'] ?? []); ?>;

        document.addEventListener('DOMContentLoaded', function() {
            if (window.loadingManager) {
                window.loadingManager.updateProgress(60);
            }

            setTimeout(function() {
                if (typeof Chart === 'undefined') {
                    showFallbackContent();
                    return;
                }

                if (!chartsInitialized) {
                    initializeCharts();
                }

                if (window.loadingManager) {
                    window.loadingManager.updateProgress(90);
                    setTimeout(() => window.loadingManager.forceComplete(), 500);
                }
            }, 2000);

            if (typeof Chart !== 'undefined' && !chartsInitialized) {
                initializeCharts();
                if (window.loadingManager) {
                    window.loadingManager.updateProgress(90);
                    setTimeout(() => window.loadingManager.forceComplete(), 200);
                }
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

            if (window.loadingManager) {
                setTimeout(() => window.loadingManager.forceComplete(), 300);
            }
        }

        function initializeCharts() {
            if (chartsInitialized) return;
            chartsInitialized = true;

            if (typeof Chart === 'undefined') {
                showFallbackContent();
                return;
            }

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
                                backgroundColor: ['#00a65a', '#dc3545'],
                                borderWidth: 2,
                                borderColor: '#ffffff'
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            cutout: '65%',
                            plugins: {
                                legend: { position: 'bottom' }
                            }
                        }
                    });
                } catch (error) {
                    console.error('Error creating user activity chart:', error);
                    userActivityCanvas.style.display = 'none';
                    document.getElementById('userActivityFallback').style.display = 'flex';
                }
            }

            const groupMembershipCanvas = document.getElementById('groupMembershipChart');
            if (groupMembershipCanvas) {
                try {
                    const groupMembershipCtx = groupMembershipCanvas.getContext('2d');
                    if (!portalGroupMembership || portalGroupMembership.length === 0) {
                        groupMembershipCanvas.style.display = 'none';
                        document.getElementById('groupMembershipFallback').style.display = 'flex';
                        return;
                    }

                    new Chart(groupMembershipCtx, {
                        type: 'bar',
                        data: {
                            labels: portalGroupMembership.map(item => item.name.length > 18 ? item.name.substring(0, 18) + '…' : item.name),
                            datasets: [{
                                label: 'Members',
                                data: portalGroupMembership.map(item => item.members),
                                backgroundColor: '#3c8dbc'
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: { legend: { display: false } },
                            scales: {
                                y: { beginAtZero: true },
                                x: { ticks: { maxRotation: 35, minRotation: 0 } }
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
            const originalText = button.innerHTML;
            button.disabled = true;
            button.innerHTML = '<span class="spinner-border spinner-border-sm mr-2" role="status"></span> Syncing...';

            const overlay = document.createElement('div');
            overlay.id = 'syncOverlay';
            overlay.className = 'position-fixed w-100 h-100 top-0 left-0 d-flex align-items-center justify-content-center loading-backdrop';
            overlay.style.zIndex = 1080;
            overlay.innerHTML = `
                <div class="bg-white rounded-lg shadow-lg p-4 text-center">
                    <div class="spinner-border text-primary mb-3" role="status"></div>
                    <p class="mb-0">Syncing data with Authentik...</p>
                </div>`;
            document.body.appendChild(overlay);

            Promise.all([
                fetch('{{ route("users.sync") }}', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } }),
                fetch('{{ route("groups.sync") }}', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } })
            ])
            .then(responses => {
                if (!responses.every(response => response.ok)) {
                    throw new Error('Failed to sync data');
                }
                showMessage('Data synced successfully!', 'success');
                setTimeout(() => window.location.reload(), 1200);
            })
            .catch(error => {
                console.error('Sync error:', error);
                showMessage('Sync failed: ' + error.message, 'error');
            })
            .finally(() => {
                const overlayEl = document.getElementById('syncOverlay');
                if (overlayEl) overlayEl.remove();
                button.disabled = false;
                button.innerHTML = originalText;
            });
        }

        function showMessage(message, type) {
            const messageDiv = document.createElement('div');
            messageDiv.className = `toast align-items-center text-white bg-${type === 'success' ? 'success' : 'danger'} border-0`;
            messageDiv.setAttribute('role', 'alert');
            messageDiv.innerHTML = `<div class="d-flex"><div class="toast-body">${message}</div><button type="button" class="ml-auto mb-1 close text-white" data-dismiss="toast" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>`;
            document.body.appendChild(messageDiv);
            $(messageDiv).toast({ delay: 3000 }).toast('show').on('hidden.bs.toast', () => messageDiv.remove());
        }
    </script>
</x-app-layout>