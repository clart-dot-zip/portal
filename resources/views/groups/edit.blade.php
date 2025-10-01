@section('title', 'Edit Group: ' . $group['name'] . ' - ' . config('app.name'))

<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Edit Group') }}: {{ $group['name'] }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('groups.show', $group['pk']) }}" 
                   class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Back to Group
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

            @if($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Group Properties Form -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Group Properties</h3>
                    
                    <form method="POST" action="{{ route('groups.update', $group['pk']) }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700">Group Name</label>
                                <input type="text" 
                                       name="name" 
                                       id="name" 
                                       value="{{ old('name', $group['name']) }}"
                                       required
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="parent" class="block text-sm font-medium text-gray-700">Parent Group</label>
                                <input type="text" 
                                       name="parent" 
                                       id="parent" 
                                       value="{{ old('parent', $group['parent'] ?? '') }}"
                                       placeholder="Leave empty for root group"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                @error('parent')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-6">
                            <div class="flex items-center">
                                <input type="checkbox" 
                                       name="is_superuser" 
                                       id="is_superuser" 
                                       value="1"
                                       {{ old('is_superuser', $group['is_superuser'] ?? false) ? 'checked' : '' }}
                                       class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                <label for="is_superuser" class="ml-2 block text-sm text-gray-900">
                                    Superuser Group
                                </label>
                            </div>
                            <p class="mt-1 text-sm text-gray-500">Members of this group will have superuser privileges</p>
                        </div>

                        <div class="mt-6">
                            <label for="attributes" class="block text-sm font-medium text-gray-700">Custom Attributes (JSON)</label>
                            <textarea name="attributes" 
                                      id="attributes" 
                                      rows="4"
                                      placeholder='{"key": "value"}'
                                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">{{ old('attributes', json_encode($group['attributes'] ?? [], JSON_PRETTY_PRINT)) }}</textarea>
                            @error('attributes')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-sm text-gray-500">Enter valid JSON format for custom attributes</p>
                        </div>

                        <div class="mt-6 flex justify-end space-x-3">
                            <a href="{{ route('groups.show', $group['pk']) }}" 
                               class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                Cancel
                            </a>
                            <button type="submit" 
                                    class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                Update Group
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Member Management -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Member Management</h3>
                    
                    <!-- Add User Form -->
                    <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                        <h4 class="text-md font-medium text-gray-900 mb-3">Add User to Group</h4>
                        <form id="add-user-form" class="flex items-end space-x-4">
                            @csrf
                            <div class="flex-1">
                                <label for="user_select" class="block text-sm font-medium text-gray-700 mb-1">Select User</label>
                                <select name="user_id" 
                                        id="user_select" 
                                        required
                                        class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    <option value="">Choose a user...</option>
                                    @foreach($availableUsers as $user)
                                        @php
                                            $isMember = collect($currentMembers)->contains('pk', $user['pk']);
                                        @endphp
                                        @if(!$isMember)
                                            <option value="{{ $user['pk'] }}">
                                                {{ $user['username'] }}{{ $user['name'] ? ' (' . $user['name'] . ')' : '' }}
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" 
                                    class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                Add User
                            </button>
                        </form>
                    </div>

                    <!-- Current Members -->
                    <div>
                        <h4 class="text-md font-medium text-gray-900 mb-3">Current Members</h4>
                        
                        @if(count($currentMembers) > 0)
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($currentMembers as $member)
                                    <div class="border border-gray-200 rounded-lg p-4 flex items-center justify-between">
                                        <div class="flex-1">
                                            <h5 class="font-medium text-gray-900">{{ $member['username'] }}</h5>
                                            @if(isset($member['name']) && $member['name'])
                                                <p class="text-sm text-gray-600">{{ $member['name'] }}</p>
                                            @endif
                                            @if(isset($member['email']) && $member['email'])
                                                <p class="text-sm text-gray-500">{{ $member['email'] }}</p>
                                            @endif
                                            <div class="mt-1">
                                                @if($member['is_active'] ?? true)
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                        Active
                                                    </span>
                                                @else
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                        Inactive
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="flex space-x-2">
                                            <a href="{{ route('users.show', $member['pk']) }}" 
                                               class="text-blue-600 hover:text-blue-900 text-sm">View</a>
                                            <button class="remove-user-btn text-red-600 hover:text-red-900 text-sm"
                                                    data-user-id="{{ $member['pk'] }}"
                                                    data-username="{{ $member['username'] }}">Remove</button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-7a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">No members in this group</h3>
                                <p class="mt-1 text-sm text-gray-500">
                                    Add users to this group using the form above.
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Add user form handler
            const addUserForm = document.getElementById('add-user-form');
            if (addUserForm) {
                addUserForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    const formData = new FormData(addUserForm);
                    const userId = formData.get('user_id');
                    
                    if (!userId) {
                        showMessage('Please select a user', 'error');
                        return;
                    }

                    // Make AJAX request
                    fetch('{{ route("groups.add-user", $group["pk"]) }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({
                            user_id: userId
                        })
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
                            // Reload page to show updated members
                            setTimeout(() => {
                                window.location.reload();
                            }, 1000);
                        } else {
                            showMessage(data.message || 'Failed to add user', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Add user error:', error);
                        showMessage('Failed to add user: ' + error.message, 'error');
                    });
                });
            }

            // Remove user button handlers
            document.querySelectorAll('.remove-user-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const userId = this.dataset.userId;
                    const username = this.dataset.username;
                    removeUser(userId, username);
                });
            });
        });

        function removeUser(userId, username) {
            if (!confirm(`Are you sure you want to remove ${username} from this group?`)) {
                return;
            }

            // Build the URL manually to avoid Laravel route generation issues
            const baseUrl = '{{ url("groups") }}';
            const groupId = '{{ $group["pk"] }}';
            const removeUrl = `${baseUrl}/${groupId}/users/${userId}`;

            fetch(removeUrl, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
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
                    // Reload page to show updated members
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    showMessage(data.message || 'Failed to remove user', 'error');
                }
            })
            .catch(error => {
                console.error('Remove user error:', error);
                showMessage('Failed to remove user: ' + error.message, 'error');
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
    </script>
</x-app-layout>