@section('title', 'Users Management - ' . config('app.name'))

<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Users Management') }}
            </h2>
            <div class="flex space-x-3">
                <a href="{{ route('users.onboard') }}" 
                   class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200 inline-flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Onboard User
                </a>
                <button id="sync-users-btn" 
                        class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200 inline-flex items-center">
                    <span id="sync-spinner" class="hidden">
                        <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </span>
                    <svg id="sync-icon" class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    <span id="sync-text">Sync Users</span>
                </button>
            </div>
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

            @if(isset($error))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ $error }}</span>
                </div>
            @endif

            <!-- Search Form -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="GET" action="{{ route('users.index') }}" class="flex items-center space-x-4">
                        <div class="flex-1">
                            <input type="text" 
                                   name="search" 
                                   value="{{ $search ?? '' }}"
                                   placeholder="Search users by username, email, or name..."
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        <button type="submit" 
                                class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200">
                            Search
                        </button>
                        @if($search ?? false)
                            <a href="{{ route('users.index') }}" 
                               class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200">
                                Clear
                            </a>
                        @endif
                    </form>
                </div>
            </div>

            <!-- Users Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-medium text-gray-900">
                            @if(isset($search) && $search)
                                Search Results for "{{ $search }}"
                            @else
                                All Users
                            @endif
                            @if(isset($pagination))
                                <span class="text-sm font-normal text-gray-500">({{ $pagination['total'] }} total)</span>
                            @else
                                <span class="text-sm font-normal text-gray-500">({{ $users->count() }} total)</span>
                            @endif
                        </h3>

                        <div class="flex items-center space-x-4">
                            <div class="flex items-center">
                                <div class="w-3 h-3 bg-green-500 rounded-full mr-2"></div>
                                <span class="text-sm text-gray-600">Synced Locally</span>
                            </div>
                            <div class="flex items-center">
                                <div class="w-3 h-3 bg-yellow-500 rounded-full mr-2"></div>
                                <span class="text-sm text-gray-600">Not Synced</span>
                            </div>
                        </div>
                    </div>

                    @if($users->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Status
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Username
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Name
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Email
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Active
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Superuser
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Portal Admin
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Last Login
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($users as $user)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="w-3 h-3 rounded-full mr-2 {{ $user['synced_locally'] ? 'bg-green-500' : 'bg-yellow-500' }}"></div>
                                                    <span class="text-sm text-gray-600">
                                                        {{ $user['synced_locally'] ? 'Synced' : 'Not Synced' }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">{{ $user['username'] }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">{{ $user['name'] ?: '-' }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">{{ $user['email'] ?: '-' }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $user['is_active'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                    {{ $user['is_active'] ? 'Active' : 'Inactive' }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($user['is_superuser'])
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                                        Yes
                                                    </span>
                                                @else
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                        No
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center space-x-2">
                                                    @if($user['is_portal_admin'])
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                            Admin
                                                        </span>
                                                    @else
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                            User
                                                        </span>
                                                    @endif
                                                    <button class="toggle-admin-btn text-xs font-medium py-1 px-2 rounded transition-colors duration-200 {{ $user['is_portal_admin'] ? 'bg-red-100 text-red-700 hover:bg-red-200' : 'bg-green-100 text-green-700 hover:bg-green-200' }}"
                                                            data-user-id="{{ $user['id'] }}"
                                                            data-username="{{ $user['username'] }}"
                                                            data-is-admin="{{ $user['is_portal_admin'] ? 'true' : 'false' }}">
                                                        {{ $user['is_portal_admin'] ? 'Remove' : 'Grant' }}
                                                    </button>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $user['last_login'] ? \Carbon\Carbon::parse($user['last_login'])->diffForHumans() : 'Never' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                                <a href="{{ route('users.show', $user['id']) }}" 
                                                   class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-1 px-3 rounded-md transition-colors duration-200 inline-block">
                                                   View
                                                </a>
                                                <a href="{{ route('users.edit', $user['id']) }}" 
                                                   class="bg-yellow-500 hover:bg-yellow-600 text-white font-medium py-1 px-3 rounded-md transition-colors duration-200 inline-block">
                                                   Edit
                                                </a>
                                                <button class="bg-red-600 hover:bg-red-700 text-white font-medium py-1 px-3 rounded-md transition-colors duration-200 delete-user-btn"
                                                        data-user-id="{{ $user['id'] }}"
                                                        data-username="{{ $user['username'] }}">
                                                        Delete
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        @if(isset($pagination) && $pagination['last_page'] > 1)
                            <div class="mt-6 flex items-center justify-between">
                                <div class="flex items-center">
                                    <span class="text-sm text-gray-700">
                                        Showing page {{ $pagination['current_page'] }} of {{ $pagination['last_page'] }}
                                        ({{ $pagination['total'] }} total users)
                                    </span>
                                </div>
                                <div class="flex space-x-2">
                                    @if($pagination['current_page'] > 1)
                                        <a href="{{ route('users.index', array_merge(request()->query(), ['page' => $pagination['current_page'] - 1])) }}" 
                                           class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                                            Previous
                                        </a>
                                    @endif
                                    
                                    @if($pagination['current_page'] < $pagination['last_page'])
                                        <a href="{{ route('users.index', array_merge(request()->query(), ['page' => $pagination['current_page'] + 1])) }}" 
                                           class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                                            Next
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endif

                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No users found</h3>
                            <p class="mt-1 text-sm text-gray-500">
                                @if(isset($search) && $search)
                                    No users match your search criteria.
                                @else
                                    Click the "Sync Users" button to load users from Authentik.
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
            const syncBtn = document.getElementById('sync-users-btn');
            const spinner = document.getElementById('sync-spinner');
            const syncIcon = document.getElementById('sync-icon');
            const syncText = document.getElementById('sync-text');

            if (syncBtn) {
                syncBtn.addEventListener('click', function() {
                    // Show loading state
                    syncBtn.disabled = true;
                    spinner.classList.remove('hidden');
                    syncIcon.classList.add('hidden');
                    syncText.textContent = 'Syncing...';

                    // Make AJAX request
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
                        // Check if response is ok
                        if (!response.ok) {
                            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                        }
                        
                        // Try to parse as JSON
                        return response.text().then(text => {
                            try {
                                return JSON.parse(text);
                            } catch (e) {
                                console.error('Response is not valid JSON:', text);
                                throw new Error('Server returned invalid JSON response');
                            }
                        });
                    })
                    .then(data => {
                        if (data.success) {
                            // Show success message
                            showMessage('Users synced successfully!', 'success');
                            // Reload page to show updated data
                            setTimeout(() => {
                                window.location.reload();
                            }, 1000);
                        } else {
                            showMessage(data.message || 'Sync failed', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Sync error:', error);
                        showMessage('Sync failed: ' + error.message, 'error');
                    })
                    .finally(() => {
                        // Reset button state
                        syncBtn.disabled = false;
                        spinner.classList.add('hidden');
                        syncIcon.classList.remove('hidden');
                        syncText.textContent = 'Sync Users';
                    });
                });
            }

            // Delete user button handlers
            document.querySelectorAll('.delete-user-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const userId = this.dataset.userId;
                    const username = this.dataset.username;
                    deleteUser(userId, username);
                });
            });

            // Toggle admin button handlers
            document.querySelectorAll('.toggle-admin-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const userId = this.dataset.userId;
                    const username = this.dataset.username;
                    const isAdmin = this.dataset.isAdmin === 'true';
                    togglePortalAdmin(userId, username, isAdmin);
                });
            });

            function deleteUser(userId, username) {
                if (!confirm(`Are you sure you want to delete user "${username}"? This action cannot be undone.`)) {
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
                        // Reload page to show updated data
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    } else {
                        showMessage(data.message || 'Failed to delete user', 'error');
                    }
                })
                .catch(error => {
                    console.error('Delete user error:', error);
                    showMessage('Failed to delete user: ' + error.message, 'error');
                });
            }

            function togglePortalAdmin(userId, username, isCurrentlyAdmin) {
                const action = isCurrentlyAdmin ? 'remove' : 'grant';
                const actionText = isCurrentlyAdmin ? 'remove Portal admin access from' : 'grant Portal admin access to';
                
                if (!confirm(`Are you sure you want to ${actionText} user "${username}"?`)) {
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
                        // Reload page to show updated data
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    } else {
                        showMessage(data.message || `Failed to ${action} Portal admin access`, 'error');
                    }
                })
                .catch(error => {
                    console.error('Toggle Portal admin error:', error);
                    showMessage(`Failed to ${action} Portal admin access: ` + error.message, 'error');
                });
            }

            function showMessage(message, type) {
                // Create message element
                const messageDiv = document.createElement('div');
                messageDiv.className = `mb-4 px-4 py-3 rounded relative ${type === 'success' ? 'bg-green-100 border border-green-400 text-green-700' : 'bg-red-100 border border-red-400 text-red-700'}`;
                messageDiv.innerHTML = `<span class="block sm:inline">${message}</span>`;

                // Insert at top of main content
                const mainContent = document.querySelector('.py-12 .max-w-7xl');
                if (mainContent) {
                    mainContent.insertBefore(messageDiv, mainContent.firstChild);

                    // Auto-remove after 5 seconds
                    setTimeout(() => {
                        messageDiv.remove();
                    }, 5000);
                }
            }
        });
    </script>
</x-app-layout>