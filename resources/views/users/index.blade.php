@section('title', 'Users Management - ' . config('app.name'))
@section('page_title', 'Users Management')

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-xl font-semibold text-fluent-neutral-30">{{ __('Users Management') }}</h1>
                <p class="text-sm text-fluent-neutral-26 mt-1">{{ __('Manage Authentik directory users') }}</p>
            </div>
            <div class="flex items-center gap-2">
                <x-fluent-button variant="primary" onclick="window.location.href='{{ route('users.onboard') }}'">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                        <path d="M8 2a1 1 0 011 1v4h4a1 1 0 110 2H9v4a1 1 0 11-2 0V9H3a1 1 0 110-2h4V3a1 1 0 011-1z"/>
                    </svg>
                    {{ __('Onboard User') }}
                </x-fluent-button>
                <x-fluent-button variant="secondary" id="sync-users-btn">
                    <svg id="sync-icon" width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                        <path fill-rule="evenodd" d="M3 2a1 1 0 011 1v1.586a6 6 0 019.201 2.414 1 1 0 11-1.885.666 4 4 0 00-6.316-1.1V9a1 1 0 01-2 0V3a1 1 0 011-1zm.006 7.046a1 1 0 011.276.61 4 4 0 006.316 1.1V7a1 1 0 112 0v6a1 1 0 01-1 1h-.058a6 6 0 01-9.143-2.414 1 1 0 01.61-1.276z" clip-rule="evenodd"/>
                    </svg>
                    <span id="sync-text">{{ __('Sync Users') }}</span>
                </x-fluent-button>
            </div>
        </div>
    </x-slot>

    @if(session('success'))
        <x-fluent-card padding="small" class="bg-green-50 border-green-200 mb-4">
            <div class="flex items-start gap-3">
                <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor" class="text-fluent-success flex-shrink-0">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <p class="text-sm font-medium text-fluent-success flex-1">{{ session('success') }}</p>
                <button onclick="this.parentElement.parentElement.remove()" class="text-green-600 hover:text-green-800">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                        <path d="M8.707 8l3.647-3.646a.5.5 0 00-.708-.708L8 7.293 4.354 3.646a.5.5 0 10-.708.708L7.293 8l-3.647 3.646a.5.5 0 00.708.708L8 8.707l3.646 3.647a.5.5 0 00.708-.708L8.707 8z"/>
                    </svg>
                </button>
            </div>
        </x-fluent-card>
    @endif

    @if(session('error') || isset($error))
        <x-fluent-card padding="small" class="bg-red-50 border-red-200 mb-4">
            <div class="flex items-start gap-3">
                <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor" class="text-fluent-error flex-shrink-0">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                <p class="text-sm font-medium text-fluent-error flex-1">{{ session('error') ?? $error ?? '' }}</p>
                <button onclick="this.parentElement.parentElement.remove()" class="text-red-600 hover:text-red-800">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                        <path d="M8.707 8l3.647-3.646a.5.5 0 00-.708-.708L8 7.293 4.354 3.646a.5.5 0 10-.708.708L7.293 8l-3.647 3.646a.5.5 0 00.708.708L8 8.707l3.646 3.647a.5.5 0 00.708-.708L8.707 8z"/>
                    </svg>
                </button>
            </div>
        </x-fluent-card>
    @endif

    {{-- Search Card --}}
    <x-fluent-card title="Directory Search" class="mb-4">
        <form method="GET" action="{{ route('users.index') }}" class="flex flex-col md:flex-row gap-3 items-end">
            <div class="flex-1">
                <x-fluent-input
                    type="text"
                    id="search"
                    name="search"
                    :value="$search ?? ''"
                    placeholder="{{ __('Search users by username, email, or name...') }}"
                    icon='<svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor"><path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"/></svg>'
                />
            </div>
            <div class="flex gap-2">
                <x-fluent-button type="submit" variant="primary">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"/>
                    </svg>
                    {{ __('Search') }}
                </x-fluent-button>
                @if($search ?? false)
                    <x-fluent-button variant="secondary" onclick="window.location.href='{{ route('users.index') }}'">
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                            <path d="M8.707 8l3.647-3.646a.5.5 0 00-.708-.708L8 7.293 4.354 3.646a.5.5 0 10-.708.708L7.293 8l-3.647 3.646a.5.5 0 00.708.708L8 8.707l3.646 3.647a.5.5 0 00.708-.708L8.707 8z"/>
                        </svg>
                        {{ __('Clear') }}
                    </x-fluent-button>
                @endif
            </div>
        </form>
    </x-fluent-card>

    {{-- Users Table Card --}}
    <x-fluent-card>
        <x-slot name="header">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                <div>
                    <h3 class="text-base font-semibold text-fluent-neutral-30">
                        @if(isset($search) && $search)
                            {{ __('Search Results for ":term"', ['term' => $search]) }}
                        @else
                            {{ __('All Users') }}
                        @endif
                    </h3>
                    <p class="text-xs text-fluent-neutral-26 mt-0.5">
                        @if(isset($pagination))
                            {{ $pagination['total'] }} {{ __('total users') }}
                        @else
                            {{ $users->count() }} {{ __('records loaded') }}
                        @endif
                    </p>
                </div>
                <div class="flex gap-2">
                    <x-fluent-badge variant="success" size="small">
                        <svg width="12" height="12" viewBox="0 0 12 12" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 3a1 1 0 00-1.707-.707L5 5.586 3.707 4.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4A1 1 0 0010 3z" clip-rule="evenodd"/>
                        </svg>
                        {{ __('Synced Locally') }}
                    </x-fluent-badge>
                    <x-fluent-badge variant="warning" size="small">
                        <svg width="12" height="12" viewBox="0 0 12 12" fill="currentColor">
                            <path fill-rule="evenodd" d="M6 2a4 4 0 100 8 4 4 0 000-8zM2 6a4 4 0 118 0 4 4 0 01-8 0zm4.5-1.5v2.293l1.354 1.353a.5.5 0 01-.708.708l-1.5-1.5A.5.5 0 015.5 7V4.5a.5.5 0 011 0z" clip-rule="evenodd"/>
                        </svg>
                        {{ __('Not Synced') }}
                    </x-fluent-badge>
                </div>
            </div>
        </x-slot>


        @if($users->count() > 0)
            <div class="overflow-x-auto -mx-6">
                <table class="fluent-table">
                    <thead>
                        <tr>
                            <th class="px-6 py-3">{{ __('Status') }}</th>
                            <th class="px-6 py-3">{{ __('Username') }}</th>
                            <th class="px-6 py-3">{{ __('Name') }}</th>
                            <th class="px-6 py-3">{{ __('Email') }}</th>
                            <th class="px-6 py-3">{{ __('Active') }}</th>
                            <th class="px-6 py-3">{{ __('Superuser') }}</th>
                            <th class="px-6 py-3">{{ __('Portal Admin') }}</th>
                            <th class="px-6 py-3">{{ __('Last Login') }}</th>
                            <th class="px-6 py-3 text-right">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                            <tr>
                                <td class="px-6 py-3">
                                    <x-fluent-badge 
                                        :variant="$user['synced_locally'] ? 'success' : 'warning'" 
                                        size="small"
                                    >
                                        {{ $user['synced_locally'] ? __('Synced') : __('Not Synced') }}
                                    </x-fluent-badge>
                                </td>
                                <td class="px-6 py-3 font-semibold whitespace-nowrap">{{ $user['username'] }}</td>
                                <td class="px-6 py-3">{{ $user['name'] ?: '-' }}</td>
                                <td class="px-6 py-3">{{ $user['email'] ?: '-' }}</td>
                                <td class="px-6 py-3">
                                    <x-fluent-badge 
                                        :variant="$user['is_active'] ? 'success' : 'error'" 
                                        size="small"
                                    >
                                        {{ $user['is_active'] ? __('Active') : __('Inactive') }}
                                    </x-fluent-badge>
                                </td>
                                <td class="px-6 py-3">
                                    <x-fluent-badge 
                                        :variant="$user['is_superuser'] ? 'brand' : 'neutral'" 
                                        size="small"
                                    >
                                        {{ $user['is_superuser'] ? __('Yes') : __('No') }}
                                    </x-fluent-badge>
                                </td>
                                <td class="px-6 py-3">
                                    <div class="flex items-center gap-2">
                                        <x-fluent-badge 
                                            :variant="$user['is_portal_admin'] ? 'info' : 'neutral'" 
                                            size="small"
                                        >
                                            {{ $user['is_portal_admin'] ? __('Admin') : __('User') }}
                                        </x-fluent-badge>
                                        <button 
                                            class="toggle-admin-btn text-xs px-2 py-1 rounded {{ $user['is_portal_admin'] ? 'text-fluent-error hover:bg-red-50' : 'text-fluent-success hover:bg-green-50' }} transition-colors font-semibold"
                                            data-user-id="{{ $user['id'] }}"
                                            data-username="{{ $user['username'] }}"
                                            data-is-admin="{{ $user['is_portal_admin'] ? 'true' : 'false' }}"
                                        >
                                            {{ $user['is_portal_admin'] ? __('Remove') : __('Grant') }}
                                        </button>
                                    </div>
                                </td>
                                <td class="px-6 py-3 text-fluent-neutral-26 text-sm">
                                    {{ $user['last_login'] ? \Carbon\Carbon::parse($user['last_login'])->diffForHumans() : __('Never') }}
                                </td>
                                <td class="px-6 py-3 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="{{ route('users.show', $user['id']) }}" class="p-2 text-fluent-brand-60 hover:bg-fluent-brand-10 rounded transition-colors" title="View">
                                            <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                                                <path d="M8 3C4.5 3 1.5 5.5 1 8c.5 2.5 3.5 5 7 5s6.5-2.5 7-5c-.5-2.5-3.5-5-7-5zm0 8a3 3 0 110-6 3 3 0 010 6z"/>
                                                <circle cx="8" cy="8" r="1.5"/>
                                            </svg>
                                        </a>
                                        <a href="{{ route('users.edit', $user['id']) }}" class="p-2 text-yellow-600 hover:bg-yellow-50 rounded transition-colors" title="Edit">
                                            <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                                                <path d="M11.013 1.427a1.75 1.75 0 012.474 0l1.086 1.086a1.75 1.75 0 010 2.474l-8.61 8.61c-.21.21-.47.364-.756.445l-3.251.93a.75.75 0 01-.927-.928l.929-3.25a1.75 1.75 0 01.445-.758l8.61-8.61z"/>
                                            </svg>
                                        </a>
                                        <button 
                                            class="delete-user-btn p-2 text-fluent-error hover:bg-red-50 rounded transition-colors"
                                            data-user-id="{{ $user['id'] }}"
                                            data-username="{{ $user['username'] }}"
                                            title="Delete"
                                        >
                                            <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                                                <path fill-rule="evenodd" d="M6.5 1.5a.5.5 0 01.5-.5h2a.5.5 0 01.5.5V2h3a.5.5 0 010 1h-.5v9.5a1.5 1.5 0 01-1.5 1.5h-5A1.5 1.5 0 014 12.5V3h-.5a.5.5 0 010-1h3v-.5zM5.5 3v9.5a.5.5 0 00.5.5h4a.5.5 0 00.5-.5V3h-5zM7 5a.5.5 0 01.5.5v5a.5.5 0 01-1 0v-5A.5.5 0 017 5zm2 0a.5.5 0 01.5.5v5a.5.5 0 01-1 0v-5A.5.5 0 019 5z" clip-rule="evenodd"/>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if(isset($pagination) && $pagination['last_page'] > 1)
                <div class="flex flex-col md:flex-row items-center justify-between gap-3 pt-4 border-t border-fluent-neutral-10">
                    <p class="text-sm text-fluent-neutral-26">
                        {{ __('Showing page :current of :last (:total users)', [
                            'current' => $pagination['current_page'],
                            'last' => $pagination['last_page'],
                            'total' => $pagination['total'],
                        ]) }}
                    </p>
                    <div class="flex gap-2">
                        @if($pagination['current_page'] > 1)
                            <x-fluent-button 
                                variant="secondary" 
                                size="small"
                                onclick="window.location.href='{{ route('users.index', array_merge(request()->query(), ['page' => $pagination['current_page'] - 1])) }}'"
                            >
                                {{ __('Previous') }}
                            </x-fluent-button>
                        @endif
                        @if($pagination['current_page'] < $pagination['last_page'])
                            <x-fluent-button 
                                variant="secondary" 
                                size="small"
                                onclick="window.location.href='{{ route('users.index', array_merge(request()->query(), ['page' => $pagination['current_page'] + 1])) }}'"
                            >
                                {{ __('Next') }}
                            </x-fluent-button>
                        @endif
                    </div>
                </div>
            @endif
        @else
            <div class="text-center py-12">
                <svg width="64" height="64" viewBox="0 0 64 64" fill="currentColor" class="mx-auto text-fluent-neutral-22 mb-4">
                    <path d="M32 32a10 10 0 100-20 10 10 0 000 20zm0 4c-10.67 0-16 5.33-16 16h32c0-10.67-5.33-16-16-16z"/>
                </svg>
                <h5 class="text-lg font-semibold text-fluent-neutral-30 mb-2">{{ __('No users found') }}</h5>
                <p class="text-sm text-fluent-neutral-26">
                    @if(isset($search) && $search)
                        {{ __('No users match your search criteria.') }}
                    @else
                        {{ __('Click the "Sync Users" button to load users from Authentik.') }}
                    @endif
                </p>
            </div>
        @endif
    </x-fluent-card>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const syncBtn = document.getElementById('sync-users-btn');
            const syncIcon = document.getElementById('sync-icon');
            const syncText = document.getElementById('sync-text');

            if (syncBtn) {
                syncBtn.addEventListener('click', function() {
                    syncBtn.disabled = true;
                    syncIcon.classList.add('animate-spin');
                    syncText.textContent = 'Syncing...';

                    fetch('{{ route("users.sync") }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            window.FluentUI.showToast(data.message || 'Users synced successfully!', 'success');
                            setTimeout(() => window.location.reload(), 1000);
                        } else {
                            window.FluentUI.showToast(data.message || 'Sync failed', 'error');
                        }
                    })
                    .catch(error => {
                        window.FluentUI.showToast('Sync failed: ' + error.message, 'error');
                    })
                    .finally(() => {
                        syncBtn.disabled = false;
                        syncIcon.classList.remove('animate-spin');
                        syncText.textContent = 'Sync Users';
                    });
                });
            }

            document.querySelectorAll('.delete-user-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const userId = this.dataset.userId;
                    const username = this.dataset.username;
                    
                    if (!confirm(`Are you sure you want to delete user "${username}"? This action cannot be undone.`)) {
                        return;
                    }

                    window.FluentUI.showLoading('Deleting user...');

                    fetch(`{{ url('users') }}/${userId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        window.FluentUI.hideLoading();
                        if (data.success) {
                            window.FluentUI.showToast(data.message, 'success');
                            setTimeout(() => window.location.reload(), 1000);
                        } else {
                            window.FluentUI.showToast(data.message || 'Failed to delete user', 'error');
                        }
                    })
                    .catch(error => {
                        window.FluentUI.hideLoading();
                        window.FluentUI.showToast('Failed to delete user: ' + error.message, 'error');
                    });
                });
            });

            document.querySelectorAll('.toggle-admin-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const userId = this.dataset.userId;
                    const username = this.dataset.username;
                    const isAdmin = this.dataset.isAdmin === 'true';
                    const action = isAdmin ? 'remove' : 'grant';
                    
                    if (!confirm(`Are you sure you want to ${action} Portal admin access for "${username}"?`)) {
                        return;
                    }

                    window.FluentUI.showLoading(`${action === 'grant' ? 'Granting' : 'Removing'} admin access...`);

                    fetch(`{{ url('users') }}/${userId}/toggle-admin`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        window.FluentUI.hideLoading();
                        if (data.success) {
                            window.FluentUI.showToast(data.message, 'success');
                            setTimeout(() => window.location.reload(), 1000);
                        } else {
                            window.FluentUI.showToast(data.message || `Failed to ${action} admin access`, 'error');
                        }
                    })
                    .catch(error => {
                        window.FluentUI.hideLoading();
                        window.FluentUI.showToast(`Failed to ${action} admin access: ` + error.message, 'error');
                    });
                });
            });
        });
    </script>
</x-app-layout>