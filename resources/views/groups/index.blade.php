@section('title', 'Groups Management - ' . config('app.name'))
@section('page_title', 'Groups Management')

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-xl font-semibold text-fluent-neutral-30">{{ __('Groups Management') }}</h1>
                <p class="text-sm text-fluent-neutral-26 mt-1">{{ __('Manage Authentik directory groups') }}</p>
            </div>
            <div class="flex items-center gap-2">
                <x-fluent-button variant="primary" onclick="window.location.href='{{ route('groups.create') }}'">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                        <path d="M8 2a1 1 0 011 1v4h4a1 1 0 110 2H9v4a1 1 0 11-2 0V9H3a1 1 0 110-2h4V3a1 1 0 011-1z"/>
                    </svg>
                    {{ __('Create Group') }}
                </x-fluent-button>
                <x-fluent-button variant="secondary" id="sync-groups-btn">
                    <svg id="sync-icon" width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                        <path fill-rule="evenodd" d="M3 2a1 1 0 011 1v1.586a6 6 0 019.201 2.414 1 1 0 11-1.885.666 4 4 0 00-6.316-1.1V9a1 1 0 01-2 0V3a1 1 0 011-1zm.006 7.046a1 1 0 011.276.61 4 4 0 006.316 1.1V7a1 1 0 112 0v6a1 1 0 01-1 1h-.058a6 6 0 01-9.143-2.414 1 1 0 01.61-1.276z" clip-rule="evenodd"/>
                    </svg>
                    <span id="sync-text">{{ __('Sync Groups') }}</span>
                </x-fluent-button>
            </div>
        </div>
    </x-slot>
            
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
                    <form method="GET" action="{{ route('groups.index') }}" class="flex items-center space-x-4">
                        <div class="flex-1">
                            <input type="text" 
                                   name="search" 
                                   value="{{ $search ?? '' }}"
                                   placeholder="Search groups by name..."
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        <button type="submit" 
                                class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200">
                            Search
                        </button>
                        @if($search ?? false)
                            <a href="{{ route('groups.index') }}" 
                               class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200">
                                Clear
                            </a>
                        @endif
                    </form>
                </div>
            </div>

            <!-- Groups Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                        @if(isset($search) && $search)
                            Search Results for "{{ $search }}"
                        @else
                            All Groups
                        @endif
                        @if(isset($pagination))
                            <span class="text-sm font-normal text-gray-500">({{ $pagination['total'] }} total)</span>
                        @endif
                    </h3>

                    @if(count($groups) > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Name
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Parent
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Members
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Superuser
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($groups as $group)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div>
                                                        <div class="text-sm font-medium text-gray-900">
                                                            {{ $group['name'] }}
                                                        </div>
                                                        <div class="text-sm text-gray-500 font-mono">
                                                            ID: {{ $group['pk'] }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                @if(isset($group['parent_name']) && $group['parent_name'])
                                                    {{ $group['parent_name'] }}
                                                @else
                                                    <span class="text-gray-400">Root Group</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                @if(isset($group['users']) && is_array($group['users']))
                                                    <span class="text-blue-600 font-medium">{{ count($group['users']) }} users</span>
                                                @else
                                                    <span class="text-gray-400">-</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($group['is_superuser'] ?? false)
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                                        Yes
                                                    </span>
                                                @else
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                        No
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                                <a href="{{ route('groups.show', $group['pk']) }}" 
                                                   class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-1 px-3 rounded-md transition-colors duration-200 inline-block">
                                                   View
                                                </a>
                                                <a href="{{ route('groups.edit', $group['pk']) }}" 
                                                   class="bg-yellow-500 hover:bg-yellow-600 text-white font-medium py-1 px-3 rounded-md transition-colors duration-200 inline-block">
                                                   Edit
                                                </a>
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
                                        ({{ $pagination['total'] }} total groups)
                                    </span>
                                </div>
                                <div class="flex space-x-2">
                                    @if($pagination['current_page'] > 1)
                                        <a href="{{ route('groups.index', array_merge(request()->query(), ['page' => $pagination['current_page'] - 1])) }}" 
                                           class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                                            Previous
                                        </a>
                                    @endif
                                    
                                    @if($pagination['current_page'] < $pagination['last_page'])
                                        <a href="{{ route('groups.index', array_merge(request()->query(), ['page' => $pagination['current_page'] + 1])) }}" 
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No groups found</h3>
                            <p class="mt-1 text-sm text-gray-500">
                                @if(isset($search) && $search)
                                    No groups match your search criteria.
                                @else
                                    Get started by syncing groups from Authentik.
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
            const syncBtn = document.getElementById('sync-groups-btn');
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
                    fetch('{{ route("groups.sync") }}', {
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
                            showMessage('Groups synced successfully!', 'success');
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
                        syncText.textContent = 'Sync Groups';
                    });
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