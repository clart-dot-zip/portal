@section('title', 'Users Management - ' . config('app.name'))

<x-app-layout>
    <x-slot name="header">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between">
            <div>
                <h1 class="h4 text-dark mb-1">{{ __('Users Management') }}</h1>
                <p class="text-muted mb-0">{{ __('Manage Authentik directory users') }}</p>
            </div>
            <div class="btn-toolbar mt-3 mt-md-0" role="toolbar">
                <div class="btn-group mr-2" role="group">
                    <a href="{{ route('users.onboard') }}" class="btn btn-portal-primary">
                        <i class="fas fa-user-plus mr-2"></i> {{ __('Onboard User') }}
                    </a>
                </div>
                <div class="btn-group" role="group">
                    <button id="sync-users-btn" type="button" class="btn btn-outline-secondary">
                        <span id="sync-spinner" class="spinner-border spinner-border-sm mr-2 d-none" role="status" aria-hidden="true"></span>
                        <i id="sync-icon" class="fas fa-sync mr-2"></i>
                        <span id="sync-text">{{ __('Sync Users') }}</span>
                    </button>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="row">
        <div class="col-12">
            
            <!-- Status Messages -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            @if(isset($error))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ $error }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <!-- Search Form -->
            <div class="card card-outline card-primary mb-4">
                <div class="card-header">
                    <h3 class="card-title mb-0">{{ __('Directory Search') }}</h3>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('users.index') }}" class="form-row align-items-end">
                        <div class="form-group col-md-8 col-lg-9 mb-2">
                            <label for="search" class="sr-only">{{ __('Search users') }}</label>
                            <input type="text"
                                   id="search"
                                   name="search"
                                   value="{{ $search ?? '' }}"
                                   placeholder="{{ __('Search users by username, email, or name...') }}"
                                   class="form-control">
                        </div>
                        <div class="form-group col-auto mb-2">
                            <button type="submit" class="btn btn-portal-primary">
                                <i class="fas fa-search mr-2"></i>{{ __('Search') }}
                            </button>
                        </div>
                        @if($search ?? false)
                            <div class="form-group col-auto mb-2">
                                <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times mr-2"></i>{{ __('Clear') }}
                                </a>
                            </div>
                        @endif
                    </form>
                </div>
            </div>

            <!-- Users Table -->
            <div class="card card-outline card-primary">
                <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                    <div>
                        <h3 class="card-title mb-0">
                            @if(isset($search) && $search)
                                {{ __('Search Results for ":term"', ['term' => $search]) }}
                            @else
                                {{ __('All Users') }}
                            @endif
                        </h3>
                        <small class="text-muted">
                            @if(isset($pagination))
                                {{ $pagination['total'] }} {{ __('total users') }}
                            @else
                                {{ $users->count() }} {{ __('records loaded') }}
                            @endif
                        </small>
                    </div>
                    <div class="mt-3 mt-md-0">
                        <span class="badge badge-success mr-2"><i class="fas fa-check-circle mr-1"></i>{{ __('Synced Locally') }}</span>
                        <span class="badge badge-warning text-dark"><i class="fas fa-clock mr-1"></i>{{ __('Not Synced') }}</span>
                    </div>
                </div>

                <div class="card-body p-0">
                    @if($users->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover table-striped mb-0 align-middle">
                                <thead class="thead-light">
                                    <tr>
                                        <th scope="col">{{ __('Status') }}</th>
                                        <th scope="col">{{ __('Username') }}</th>
                                        <th scope="col">{{ __('Name') }}</th>
                                        <th scope="col">{{ __('Email') }}</th>
                                        <th scope="col">{{ __('Active') }}</th>
                                        <th scope="col">{{ __('Superuser') }}</th>
                                        <th scope="col">{{ __('Portal Admin') }}</th>
                                        <th scope="col">{{ __('Last Login') }}</th>
                                        <th scope="col" class="text-right">{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($users as $user)
                                        <tr>
                                            <td>
                                                <span class="badge badge-{{ $user['synced_locally'] ? 'success' : 'warning' }} text-uppercase">
                                                    {{ $user['synced_locally'] ? __('Synced') : __('Not Synced') }}
                                                </span>
                                            </td>
                                            <td class="text-nowrap font-weight-bold">{{ $user['username'] }}</td>
                                            <td>{{ $user['name'] ?: '-' }}</td>
                                            <td>{{ $user['email'] ?: '-' }}</td>
                                            <td>
                                                <span class="badge badge-{{ $user['is_active'] ? 'success' : 'danger' }}">
                                                    {{ $user['is_active'] ? __('Active') : __('Inactive') }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge badge-{{ $user['is_superuser'] ? 'primary' : 'secondary' }}">
                                                    {{ $user['is_superuser'] ? __('Yes') : __('No') }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <span class="badge badge-{{ $user['is_portal_admin'] ? 'info' : 'secondary' }} mr-2">
                                                        {{ $user['is_portal_admin'] ? __('Admin') : __('User') }}
                                                    </span>
                                                    <button class="btn btn-sm toggle-admin-btn {{ $user['is_portal_admin'] ? 'btn-outline-danger' : 'btn-outline-success' }}"
                                                            data-user-id="{{ $user['id'] }}"
                                                            data-username="{{ $user['username'] }}"
                                                            data-is-admin="{{ $user['is_portal_admin'] ? 'true' : 'false' }}">
                                                        {{ $user['is_portal_admin'] ? __('Remove') : __('Grant') }}
                                                    </button>
                                                </div>
                                            </td>
                                            <td class="text-muted">
                                                {{ $user['last_login'] ? \Carbon\Carbon::parse($user['last_login'])->diffForHumans() : __('Never') }}
                                            </td>
                                            <td class="text-right text-nowrap">
                                                <a href="{{ route('users.show', $user['id']) }}" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('users.edit', $user['id']) }}" class="btn btn-sm btn-warning text-white">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button class="btn btn-sm btn-danger delete-user-btn"
                                                        data-user-id="{{ $user['id'] }}"
                                                        data-username="{{ $user['username'] }}">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @if(isset($pagination) && $pagination['last_page'] > 1)
                            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between p-3">
                                <div class="text-muted small mb-2 mb-md-0">
                                    {{ __('Showing page :current of :last (:total users)', [
                                        'current' => $pagination['current_page'],
                                        'last' => $pagination['last_page'],
                                        'total' => $pagination['total'],
                                    ]) }}
                                </div>
                                <div>
                                    @if($pagination['current_page'] > 1)
                                        <a href="{{ route('users.index', array_merge(request()->query(), ['page' => $pagination['current_page'] - 1])) }}" class="btn btn-outline-secondary btn-sm mr-2">
                                            {{ __('Previous') }}
                                        </a>
                                    @endif
                                    @if($pagination['current_page'] < $pagination['last_page'])
                                        <a href="{{ route('users.index', array_merge(request()->query(), ['page' => $pagination['current_page'] + 1])) }}" class="btn btn-outline-secondary btn-sm">
                                            {{ __('Next') }}
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <h5>{{ __('No users found') }}</h5>
                            <p class="text-muted mb-0">
                                @if(isset($search) && $search)
                                    {{ __('No users match your search criteria.') }}
                                @else
                                    {{ __('Click the "Sync Users" button to load users from Authentik.') }}
                                @endif
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const userMessages = {
                syncing: 'Syncing...',
                syncUsers: 'Sync Users',
                syncSuccess: 'Users synced successfully!',
                syncFailed: 'Sync failed',
                syncFailedPrefix: 'Sync failed:',
                deleteConfirm: 'Are you sure you want to delete user ":username"? This action cannot be undone.',
                deleteFailed: 'Failed to delete user',
                deleteFailedPrefix: 'Failed to delete user:',
                grantConfirm: 'Are you sure you want to grant Portal admin access to ":username"?',
                removeConfirm: 'Are you sure you want to remove Portal admin access from ":username"?',
                portalAdminFailed: 'Failed to :action Portal admin access',
                portalAdminFailedPrefix: 'Failed to :action Portal admin access:'
            };
            const syncBtn = document.getElementById('sync-users-btn');
            const spinner = document.getElementById('sync-spinner');
            const syncIcon = document.getElementById('sync-icon');
            const syncText = document.getElementById('sync-text');

            if (syncBtn) {
                syncBtn.addEventListener('click', function() {
                    syncBtn.disabled = true;
                    spinner.classList.remove('d-none');
                    syncIcon.classList.add('d-none');
                    syncText.textContent = userMessages.syncing;

                    fetch('{{ route("users.sync") }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            showMessage(userMessages.syncSuccess, 'success');
                            setTimeout(() => window.location.reload(), 1000);
                        } else {
                            showMessage(data.message || userMessages.syncFailed, 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Sync error:', error);
                        showMessage(`${userMessages.syncFailedPrefix} ${error.message}`, 'error');
                    })
                    .finally(() => {
                        syncBtn.disabled = false;
                        spinner.classList.add('d-none');
                        syncIcon.classList.remove('d-none');
                        syncText.textContent = userMessages.syncUsers;
                    });
                });
            }

            document.querySelectorAll('.delete-user-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const userId = this.dataset.userId;
                    const username = this.dataset.username;
                    deleteUser(userId, username);
                });
            });

            document.querySelectorAll('.toggle-admin-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const userId = this.dataset.userId;
                    const username = this.dataset.username;
                    const isAdmin = this.dataset.isAdmin === 'true';
                    togglePortalAdmin(userId, username, isAdmin);
                });
            });

            function deleteUser(userId, username) {
                if (!confirm(userMessages.deleteConfirm.replace(':username', username))) {
                    return;
                }

                fetch(`{{ url('users') }}/${userId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        showMessage(data.message, 'success');
                        setTimeout(() => window.location.reload(), 1000);
                    } else {
                        showMessage(data.message || userMessages.deleteFailed, 'error');
                    }
                })
                .catch(error => {
                    console.error('Delete user error:', error);
                    showMessage(`${userMessages.deleteFailedPrefix} ${error.message}`, 'error');
                });
            }

            function togglePortalAdmin(userId, username, isCurrentlyAdmin) {
                const action = isCurrentlyAdmin ? 'remove' : 'grant';
                const actionText = isCurrentlyAdmin ? userMessages.removeConfirm : userMessages.grantConfirm;

                if (!confirm(actionText.replace(':username', username))) {
                    return;
                }

                fetch(`{{ url('users') }}/${userId}/toggle-admin`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        showMessage(data.message, 'success');
                        setTimeout(() => window.location.reload(), 1000);
                    } else {
                        showMessage(data.message || userMessages.portalAdminFailed.replace(':action', action), 'error');
                    }
                })
                .catch(error => {
                    console.error('Toggle Portal admin error:', error);
                    showMessage(`${userMessages.portalAdminFailedPrefix.replace(':action', action)} ${error.message}`, 'error');
                });
            }

            function showMessage(message, type) {
                const toast = document.createElement('div');
                toast.className = `toast text-white bg-${type === 'success' ? 'success' : 'danger'} border-0`;
                toast.style.position = 'fixed';
                toast.style.top = '1rem';
                toast.style.right = '1rem';
                toast.style.zIndex = 2000;
                toast.innerHTML = `
                    <div class="d-flex align-items-center">
                        <div class="toast-body">${message}</div>
                        <button type="button" class="ml-2 mb-1 close text-white" data-dismiss="toast" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>`;

                document.body.appendChild(toast);
                const toastInstance = new bootstrap.Toast(toast, { delay: 4000 });
                toast.addEventListener('hidden.bs.toast', () => toast.remove());
                toastInstance.show();
            }
        });
    </script>
</x-app-layout>