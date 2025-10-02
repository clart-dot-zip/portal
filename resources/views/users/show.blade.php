@section('title', 'User Details: ' . $authentikUser['username'] . ' - ' . config('app.name'))

<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('User Details') }}: {{ $authentikUser['username'] }}
            </h2>
            <div class="flex space-x-3">
                <a href="{{ route('users.edit', $authentikUser['pk']) }}" 
                   class="bg-yellow-500 hover:bg-yellow-600 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200 inline-flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Edit User
                </a>
                <button class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200 send-recovery-btn"
                        data-user-id="{{ $authentikUser['pk'] }}"
                        data-username="{{ $authentikUser['username'] }}"
                        data-email="{{ $authentikUser['email'] }}">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                    Send Password Recovery
                </button>
                <button class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200 delete-user-btn"
                        data-user-id="{{ $authentikUser['pk'] }}"
                        data-username="{{ $authentikUser['username'] }}">
                    Delete User
                </button>
                <a href="{{ route('users.index') }}" 
                   class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200 inline-flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Users
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- User Information Card -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">User Information</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <dl class="space-y-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Username</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $authentikUser['username'] }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Name</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $authentikUser['name'] ?: 'Not set' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Email</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $authentikUser['email'] ?: 'Not set' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">User ID</dt>
                                    <dd class="mt-1 text-sm text-gray-900 font-mono">{{ $authentikUser['pk'] }}</dd>
                                </div>
                            </dl>
                        </div>
                        
                        <div>
                            <dl class="space-y-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Status</dt>
                                    <dd class="mt-1">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $authentikUser['is_active'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $authentikUser['is_active'] ? 'Active' : 'Inactive' }}
                                        </span>
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Superuser</dt>
                                    <dd class="mt-1">
                                        @if($authentikUser['is_superuser'])
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                                Yes
                                            </span>
                                        @else
                                            <span class="text-sm text-gray-500">No</span>
                                        @endif
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Last Login</dt>
                                    <dd class="mt-1 text-sm text-gray-900">
                                        @if($authentikUser['last_login'])
                                            {{ \Carbon\Carbon::parse($authentikUser['last_login'])->format('M j, Y g:i A') }}
                                            <span class="text-gray-500">({{ \Carbon\Carbon::parse($authentikUser['last_login'])->diffForHumans() }})</span>
                                        @else
                                            Never
                                        @endif
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Date Joined</dt>
                                    <dd class="mt-1 text-sm text-gray-900">
                                        @if($authentikUser['date_joined'])
                                            {{ \Carbon\Carbon::parse($authentikUser['date_joined'])->format('M j, Y g:i A') }}
                                        @else
                                            Unknown
                                        @endif
                                    </dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Local Sync Status -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Local Sync Status</h3>
                    
                    @if($localUser)
                        <div class="flex items-center mb-4">
                            <div class="w-3 h-3 bg-green-500 rounded-full mr-2"></div>
                            <span class="text-sm text-green-600 font-medium">User is synced locally</span>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <dl class="space-y-4">
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Local ID</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $localUser->id }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Local Name</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $localUser->name ?: 'Not set' }}</dd>
                                    </div>
                                </dl>
                            </div>
                            <div>
                                <dl class="space-y-4">
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Local Email</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $localUser->email ?: 'Not set' }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Last Synced</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $localUser->updated_at->diffForHumans() }}</dd>
                                    </div>
                                </dl>
                            </div>
                        </div>
                    @else
                        <div class="flex items-center mb-4">
                            <div class="w-3 h-3 bg-yellow-500 rounded-full mr-2"></div>
                            <span class="text-sm text-yellow-600 font-medium">User is not synced locally</span>
                        </div>
                        <p class="text-sm text-gray-600">This user exists in Authentik but has not been synced to the local database.</p>
                    @endif
                </div>
            </div>

            <!-- Groups -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Groups</h3>
                    
                    @if(count($groups) > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($groups as $group)
                                <div class="border border-gray-200 rounded-lg p-4">
                                    <h4 class="font-medium text-gray-900">{{ $group['name'] }}</h4>
                                    @if(isset($group['parent_name']) && $group['parent_name'])
                                        <p class="text-sm text-gray-500 mt-1">Parent: {{ $group['parent_name'] }}</p>
                                    @endif
                                    @if($group['is_superuser'])
                                        <span class="mt-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                            Superuser Group
                                        </span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-500">This user is not a member of any groups.</p>
                    @endif
                </div>
            </div>

            <!-- Additional Attributes -->
            @if(isset($authentikUser['attributes']) && count($authentikUser['attributes']) > 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Additional Attributes</h3>
                        
                        <div class="bg-gray-50 rounded-lg p-4">
                            <pre class="text-sm text-gray-700 whitespace-pre-wrap">{{ json_encode($authentikUser['attributes'], JSON_PRETTY_PRINT) }}</pre>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Delete user button handler
            const deleteBtn = document.querySelector('.delete-user-btn');
            if (deleteBtn) {
                deleteBtn.addEventListener('click', function() {
                    const userId = this.dataset.userId;
                    const username = this.dataset.username;
                    deleteUser(userId, username);
                });
            }

            // Send password recovery button handler
            const recoveryBtn = document.querySelector('.send-recovery-btn');
            if (recoveryBtn) {
                recoveryBtn.addEventListener('click', function() {
                    const userId = this.dataset.userId;
                    const username = this.dataset.username;
                    const email = this.dataset.email;
                    sendPasswordRecovery(userId, username, email);
                });
            }

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
                        // Redirect to users index after successful deletion
                        setTimeout(() => {
                            window.location.href = '{{ route("users.index") }}';
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

            function sendPasswordRecovery(userId, username, email) {
                if (!confirm(`Send password recovery link to ${username} (${email})?`)) {
                    return;
                }

                // Disable button and show loading state
                const recoveryBtn = document.querySelector('.send-recovery-btn');
                const originalText = recoveryBtn.innerHTML;
                recoveryBtn.disabled = true;
                recoveryBtn.innerHTML = `
                    <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Sending...
                `;

                fetch(`{{ url('users') }}/${userId}/send-recovery`, {
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
                    } else {
                        showMessage(data.message || 'Failed to send password recovery email', 'error');
                    }
                })
                .catch(error => {
                    console.error('Send recovery error:', error);
                    showMessage('Failed to send password recovery email: ' + error.message, 'error');
                })
                .finally(() => {
                    // Restore button state
                    recoveryBtn.disabled = false;
                    recoveryBtn.innerHTML = originalText;
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