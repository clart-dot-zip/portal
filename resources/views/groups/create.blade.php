@section('title', 'Create New Group - ' . config('app.name'))

<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Create New Group') }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('groups.index') }}" 
                   class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline inline-flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Groups
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            
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

            <!-- Create Group Form -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-6">Group Information</h3>

                    <form method="POST" action="{{ route('groups.store') }}" class="space-y-6">
                        @csrf

                        <!-- Group Name -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                Group Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name') }}"
                                   required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-500 @enderror"
                                   placeholder="Enter group name">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Parent Group -->
                        <div>
                            <label for="parent" class="block text-sm font-medium text-gray-700 mb-2">
                                Parent Group
                            </label>
                            <select id="parent" 
                                    name="parent" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('parent') border-red-500 @enderror">
                                <option value="">None (Root Level Group)</option>
                                @foreach($parentGroups as $group)
                                    <option value="{{ $group['pk'] }}" {{ old('parent') == $group['pk'] ? 'selected' : '' }}>
                                        {{ $group['name'] }}
                                    </option>
                                @endforeach
                            </select>
                            @error('parent')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-sm text-gray-500">
                                Select a parent group to create a hierarchical structure, or leave empty for a root-level group.
                            </p>
                        </div>

                        <!-- Superuser Status -->
                        <div>
                            <div class="flex items-center">
                                <input type="checkbox" 
                                       id="is_superuser" 
                                       name="is_superuser" 
                                       value="1"
                                       {{ old('is_superuser') ? 'checked' : '' }}
                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <label for="is_superuser" class="ml-2 block text-sm font-medium text-gray-700">
                                    Superuser Group
                                </label>
                            </div>
                            <p class="mt-1 text-sm text-gray-500">
                                Members of superuser groups have administrative privileges across all applications.
                            </p>
                            @error('is_superuser')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Custom Attributes -->
                        <div>
                            <label for="attributes" class="block text-sm font-medium text-gray-700 mb-2">
                                Custom Attributes
                            </label>
                            <textarea id="attributes" 
                                      name="attributes" 
                                      rows="6"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent font-mono text-sm @error('attributes') border-red-500 @enderror"
                                      placeholder='{"key": "value", "department": "IT"}'>{{ old('attributes') }}</textarea>
                            @error('attributes')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-sm text-gray-500">
                                Optional JSON object for storing additional group metadata. Must be valid JSON format.
                            </p>
                        </div>

                        <!-- Form Actions -->
                        <div class="flex justify-between items-center pt-6 border-t border-gray-200">
                            <div class="text-sm text-gray-500">
                                <span class="text-red-500">*</span> Required fields
                            </div>
                            <div class="flex space-x-3">
                                <a href="{{ route('groups.index') }}" 
                                   class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                    Cancel
                                </a>
                                <button type="submit" 
                                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline inline-flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    Create Group
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Help Section -->
            <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-blue-800">
                            Group Creation Tips
                        </h3>
                        <div class="mt-2 text-sm text-blue-700">
                            <ul class="list-disc list-inside space-y-1">
                                <li><strong>Group Names:</strong> Choose descriptive names that reflect the group's purpose (e.g., "Marketing Team", "Administrators")</li>
                                <li><strong>Hierarchy:</strong> Use parent groups to organize departments or organizational structure</li>
                                <li><strong>Superuser Groups:</strong> Use sparingly - only for groups that need full administrative access</li>
                                <li><strong>Custom Attributes:</strong> Store additional metadata like department codes, cost centers, or contact information</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // JSON validation for attributes field
            const attributesField = document.getElementById('attributes');
            
            if (attributesField) {
                attributesField.addEventListener('blur', function() {
                    const value = this.value.trim();
                    
                    if (value && value !== '') {
                        try {
                            JSON.parse(value);
                            // Valid JSON - remove error styling
                            this.classList.remove('border-red-500');
                            this.classList.add('border-green-500');
                            
                            // Remove any existing error message
                            const existingError = this.parentNode.querySelector('.json-error');
                            if (existingError) {
                                existingError.remove();
                            }
                        } catch (e) {
                            // Invalid JSON - add error styling
                            this.classList.remove('border-green-500');
                            this.classList.add('border-red-500');
                            
                            // Add error message if not already present
                            const existingError = this.parentNode.querySelector('.json-error');
                            if (!existingError) {
                                const errorMsg = document.createElement('p');
                                errorMsg.className = 'mt-1 text-sm text-red-600 json-error';
                                errorMsg.textContent = 'Invalid JSON format: ' + e.message;
                                this.parentNode.appendChild(errorMsg);
                            }
                        }
                    } else {
                        // Empty value - reset styling
                        this.classList.remove('border-red-500', 'border-green-500');
                        const existingError = this.parentNode.querySelector('.json-error');
                        if (existingError) {
                            existingError.remove();
                        }
                    }
                });
            }
        });
    </script>
</x-app-layout>