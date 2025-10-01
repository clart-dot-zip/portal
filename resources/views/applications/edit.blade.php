<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Manage Application Access') }}: {{ $application['name'] }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('applications.show', $application['pk']) }}" 
                   class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    View Details
                </a>
                <a href="{{ route('applications.index') }}" 
                   class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
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

            <!-- Application Info Bar -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-center space-x-4">
                        @if(isset($application['meta_icon']) && $application['meta_icon'])
                            <img src="{{ $application['meta_icon'] }}" 
                                 alt="{{ $application['name'] }} icon" 
                                 class="h-12 w-12 rounded-lg shadow-sm border border-gray-200">
                        @else
                            <div class="h-12 w-12 rounded-lg bg-gray-100 flex items-center justify-center">
                                <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                </svg>
                            </div>
                        @endif
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">{{ $application['name'] }}</h3>
                            <p class="text-sm text-gray-500">{{ $application['slug'] ?? 'No slug' }} • {{ $application['pk'] }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Assign Group Access -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-6">Assign Group Access</h3>
                    
                    <form id="assignGroupForm" class="space-y-4">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="md:col-span-2">
                                <label for="group_select" class="block text-sm font-medium text-gray-700">Select Group</label>
                                <select id="group_select" name="group_id" 
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Choose a group...</option>
                                    @foreach($groups as $group)
                                        <option value="{{ $group['pk'] }}">{{ $group['name'] }}</option>
                                    @endforeach
                                </select>
                                <p class="mt-1 text-sm text-gray-500">Select a group to grant access to this application</p>
                            </div>
                            <div class="flex flex-col justify-start">
                                <label class="block text-sm font-medium text-gray-700 mb-1 opacity-0">Action</label>
                                <button type="submit" 
                                        class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded mt-1">
                                    Assign Group Access
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Assign User Access -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-6">User Access Information</h3>
                    
                    <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-blue-800">
                                    Direct User Assignment Not Supported
                                </h3>
                                <div class="mt-2 text-sm text-blue-700">
                                    <p>
                                        Authentik doesn't support direct user-to-application assignment. 
                                        Users receive access through group membership. To grant access to a user:
                                    </p>
                                    <ol class="list-decimal list-inside mt-2 space-y-1">
                                        <li>Assign a group to this application using the "Assign Group Access" section above</li>
                                        <li>Add the user to that group in the Groups management section</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Current Access Policies -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-medium text-gray-900">Current Access Policies</h3>
                        <button onclick="loadAccessPolicies()" 
                                class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-1 px-3 rounded text-sm">
                            Refresh
                        </button>
                    </div>
                    
                    <!-- Loading State -->
                    <div id="policies-loading" class="text-center py-8">
                        <div class="inline-flex items-center px-4 py-2 font-semibold leading-6 text-sm shadow rounded-md text-gray-500 bg-white">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Loading policies...
                        </div>
                    </div>

                    <!-- Policies List -->
                    <div id="policies-container" class="hidden">
                        <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
                            <table class="min-w-full divide-y divide-gray-300">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Policy Name</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Target</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="policies-table-body" class="bg-white divide-y divide-gray-200">
                                    <!-- Policies will be loaded here -->
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Empty State -->
                    <div id="policies-empty" class="hidden text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No access policies</h3>
                        <p class="mt-1 text-sm text-gray-500">This application currently has no access policies assigned.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const applicationId = '{{ $application["pk"] }}';

        document.addEventListener('DOMContentLoaded', function() {
            // Load current access policies
            loadAccessPolicies();

            // Setup form handlers
            setupFormHandlers();
        });

        function setupFormHandlers() {
            // Group assignment form
            document.getElementById('assignGroupForm').addEventListener('submit', function(e) {
                e.preventDefault();
                const groupId = document.getElementById('group_select').value;
                if (!groupId) {
                    alert('Please select a group');
                    return;
                }
                assignGroupAccess(groupId);
            });
        }

        function assignGroupAccess(groupId) {
            const button = document.querySelector('#assignGroupForm button[type="submit"]');
            const originalText = button.textContent;
            button.textContent = 'Assigning...';
            button.disabled = true;

            fetch(`{{ route('applications.assign-group', $application['pk']) }}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    group_id: groupId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showMessage('Group access assigned successfully', 'success');
                    document.getElementById('group_select').value = '';
                    loadAccessPolicies();
                } else {
                    showMessage(data.message || 'Failed to assign group access', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showMessage('An error occurred while assigning group access', 'error');
            })
            .finally(() => {
                button.textContent = originalText;
                button.disabled = false;
            });
        }

        function removeAccess(policyId, policyName) {
            if (!confirm(`Are you sure you want to remove access policy "${policyName}"?`)) {
                return;
            }

            fetch(`{{ route('applications.remove-access', $application['pk']) }}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    policy_id: policyId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showMessage('Access removed successfully', 'success');
                    loadAccessPolicies();
                } else {
                    showMessage(data.message || 'Failed to remove access', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showMessage('An error occurred while removing access', 'error');
            });
        }

        function loadAccessPolicies() {
            // Show loading state
            document.getElementById('policies-loading').classList.remove('hidden');
            document.getElementById('policies-container').classList.add('hidden');
            document.getElementById('policies-empty').classList.add('hidden');

            // Load current access from server data
            const currentAccess = {!! json_encode($currentAccess ?? []) !!};
            const application = {!! json_encode($application) !!};
            
            console.log('Current access:', currentAccess);
            console.log('Application:', application);
            
            setTimeout(() => {
                document.getElementById('policies-loading').classList.add('hidden');
                
                const tableBody = document.getElementById('policies-table-body');
                tableBody.innerHTML = '';
                
                let hasAnyPolicies = false;
                
                // Add current access information (now an array)
                if (currentAccess && Array.isArray(currentAccess) && currentAccess.length > 0) {
                    hasAnyPolicies = true;
                    currentAccess.forEach(access => {
                        const row = createAccessRow(access);
                        tableBody.appendChild(row);
                    });
                } else if (currentAccess && !Array.isArray(currentAccess)) {
                    // Handle old single object format for backwards compatibility
                    hasAnyPolicies = true;
                    const row = createAccessRow(currentAccess);
                    tableBody.appendChild(row);
                }
                
                if (hasAnyPolicies) {
                    document.getElementById('policies-container').classList.remove('hidden');
                } else {
                    document.getElementById('policies-empty').classList.remove('hidden');
                }
            }, 500);
        }
        
        function createAccessRow(access) {
            const row = document.createElement('tr');
            
            if (access.type === 'group') {
                row.innerHTML = `
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        Group Access Assignment
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            Group
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        ${access.group_name}
                        ${access.enabled === false ? '<span class="ml-2 text-red-500">(Disabled)</span>' : ''}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <button onclick="removePolicyBinding('${access.binding_id}')" 
                                class="text-red-600 hover:text-red-900">
                            Remove
                        </button>
                    </td>
                `;
            } else if (access.type === 'user') {
                row.innerHTML = `
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        User Access Assignment
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            User
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        ${access.user_name}
                        ${access.enabled === false ? '<span class="ml-2 text-red-500">(Disabled)</span>' : ''}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <button onclick="removePolicyBinding('${access.binding_id}')" 
                                class="text-red-600 hover:text-red-900">
                            Remove
                        </button>
                    </td>
                `;
            } else if (access.type === 'default') {
                row.innerHTML = `
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        Default Access Policy
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                            Default
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        ${access.description}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <span class="text-gray-400">System Default</span>
                    </td>
                `;
            }
            
            return row;
        }
        
        function removePolicyBinding(bindingId) {
            if (!confirm('Are you sure you want to remove this access policy?')) {
                return;
            }

            fetch(`{{ route('applications.remove-access', $application['pk']) }}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    policy_id: bindingId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showMessage('Access policy removed successfully', 'success');
                    loadAccessPolicies();
                } else {
                    showMessage(data.message || 'Failed to remove access policy', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showMessage('An error occurred while removing access policy', 'error');
            });
        }

        function showMessage(message, type) {
            // Remove existing messages
            const existingMessages = document.querySelectorAll('.alert-message');
            existingMessages.forEach(msg => msg.remove());

            // Create new message
            const messageDiv = document.createElement('div');
            messageDiv.className = `alert-message mb-4 px-4 py-3 rounded relative ${type === 'success' ? 'bg-green-100 border border-green-400 text-green-700' : 'bg-red-100 border border-red-400 text-red-700'}`;
            messageDiv.innerHTML = `<span class="block sm:inline">${message}</span>`;

            // Insert at the top of the main content area (after the header)
            const mainContent = document.querySelector('.py-12 .max-w-7xl');
            if (mainContent && mainContent.firstChild) {
                mainContent.insertBefore(messageDiv, mainContent.firstChild);
            } else {
                // Fallback to body if container not found
                document.body.insertBefore(messageDiv, document.body.firstChild);
            }

            // Auto-remove after 5 seconds
            setTimeout(() => {
                messageDiv.remove();
            }, 5000);
        }
    </script>
</x-app-layout>