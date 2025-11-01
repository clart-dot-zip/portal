@section('title', 'Onboard New User - ' . config('app.name'))

<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Onboard New User') }}
            </h2>
            <div>
                <a href="{{ route('users.index') }}" 
                   class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Back to Users
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

            <!-- Onboarding Form -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-6">User Information</h3>
                    
                    <form id="onboard-form" action="{{ route('users.onboard.process') }}" method="POST" class="space-y-6">
                        @csrf
                        
                        <!-- Basic Information -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="username" class="block text-sm font-medium text-gray-700">Username *</label>
                                <input type="text" name="username" id="username" required 
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                       placeholder="e.g., john.doe">
                                @error('username')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700">Email Address *</label>
                                <input type="email" name="email" id="email" required 
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                       placeholder="john.doe@example.com">
                                @error('email')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="server_username" class="block text-sm font-medium text-gray-700">Server Username *</label>
                                <input type="text" name="server_username" id="server_username" required
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                       placeholder="linux account name"
                                       pattern="[a-z_][a-z0-9_-]*"
                                       maxlength="64">
                                <p class="mt-1 text-xs text-gray-500">Name of the account on the dedicated Linux server.</p>
                                @error('server_username')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="first_name" class="block text-sm font-medium text-gray-700">First Name</label>
                                <input type="text" name="first_name" id="first_name"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                       placeholder="John">
                                @error('first_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="last_name" class="block text-sm font-medium text-gray-700">Last Name</label>
                                <input type="text" name="last_name" id="last_name"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                       placeholder="Doe">
                                @error('last_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Additional User Attributes -->
                        <div class="border-t pt-6">
                            <h4 class="text-md font-medium text-gray-900 mb-4">Additional Information</h4>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="path" class="block text-sm font-medium text-gray-700">Path/Department</label>
                                    <input type="text" name="path" id="path"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                           placeholder="e.g., engineering">
                                    @error('path')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="type" class="block text-sm font-medium text-gray-700">User Type</label>
                                    <select name="type" id="type"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="internal">Internal</option>
                                        <option value="external">External</option>
                                        <option value="service_account">Service Account</option>
                                    </select>
                                    @error('type')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Group Assignment -->
                        <div class="border-t pt-6">
                            <h4 class="text-md font-medium text-gray-900 mb-4">Group Membership</h4>
                            
                            <div class="space-y-3">
                                @if(isset($groups) && count($groups) > 0)
                                    @foreach($groups as $group)
                                        <div class="flex items-center">
                                            <input type="checkbox" name="groups[]" value="{{ $group['pk'] }}" 
                                                   id="group_{{ $group['pk'] }}"
                                                   class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                            <label for="group_{{ $group['pk'] }}" class="ml-2 block text-sm text-gray-900">
                                                {{ $group['name'] }}
                                                @if(isset($group['users']) && is_array($group['users']))
                                                    <span class="text-gray-500">({{ count($group['users']) }} members)</span>
                                                @endif
                                            </label>
                                        </div>
                                    @endforeach
                                @else
                                    <p class="text-gray-500">No groups available for assignment</p>
                                @endif
                            </div>
                        </div>

                        <!-- Password Settings -->
                        <div class="border-t pt-6">
                            <h4 class="text-md font-medium text-gray-900 mb-4">Password & Access</h4>
                            
                            <div class="space-y-4">
                                <div class="flex items-center">
                                    <input type="checkbox" name="generate_password" id="generate_password" checked
                                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                    <label for="generate_password" class="ml-2 block text-sm text-gray-900">
                                        Generate secure password automatically
                                    </label>
                                </div>

                                <div class="flex items-center">
                                    <input type="checkbox" name="send_email" id="send_email" checked
                                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                    <label for="send_email" class="ml-2 block text-sm text-gray-900">
                                        Send welcome email with login credentials
                                    </label>
                                </div>

                                <div class="flex items-center">
                                    <input type="checkbox" name="force_password_change" id="force_password_change" checked
                                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                    <label for="force_password_change" class="ml-2 block text-sm text-gray-900">
                                        Require password change on first login
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="border-t pt-6 flex justify-end space-x-3">
                            <a href="{{ route('users.index') }}" 
                               class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200">
                                Cancel
                            </a>
                            <button type="submit" id="submit-btn"
                                    class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200 inline-flex items-center">
                                <span id="submit-spinner" class="hidden">
                                    <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                </span>
                                Create User
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Password Display Modal -->
    <div id="password-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100">
                    <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h3 class="text-lg leading-6 font-medium text-gray-900 mt-4">User Created Successfully!</h3>
                <div class="mt-4 px-7 py-3">
                    <p class="text-sm text-gray-500 mb-4">
                        The user has been created. Here are their login credentials:
                    </p>
                    <div class="bg-gray-100 p-4 rounded-lg">
                        <div class="mb-2">
                            <label class="block text-xs font-medium text-gray-700">Username:</label>
                            <div class="flex items-center">
                                <span id="modal-username" class="font-mono text-sm"></span>
                                <button onclick="copyToClipboard('modal-username')" class="ml-2 text-blue-600 hover:text-blue-800">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <div class="mb-2">
                            <label class="block text-xs font-medium text-gray-700">Password:</label>
                            <div class="flex items-center">
                                <span id="modal-password" class="font-mono text-sm bg-yellow-100 px-2 py-1 rounded"></span>
                                <button onclick="copyToClipboard('modal-password')" class="ml-2 text-blue-600 hover:text-blue-800">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <div class="text-xs text-gray-500 mt-2">
                            Make sure to save these credentials as they will not be displayed again.
                        </div>
                    </div>
                </div>
                <div class="items-center px-4 py-3">
                    <button onclick="closePasswordModal()" 
                            class="px-4 py-2 bg-blue-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-300">
                        Continue
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('onboard-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const submitBtn = document.getElementById('submit-btn');
            const submitSpinner = document.getElementById('submit-spinner');
            
            submitBtn.disabled = true;
            submitSpinner.classList.remove('hidden');
            
            // Create FormData from the form
            const formData = new FormData(this);
            
            // Convert checkboxes to proper boolean values
            formData.set('generate_password', document.getElementById('generate_password').checked ? '1' : '0');
            formData.set('send_email', document.getElementById('send_email').checked ? '1' : '0');
            formData.set('force_password_change', document.getElementById('force_password_change').checked ? '1' : '0');
            
            fetch(this.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (data.password) {
                        document.getElementById('modal-username').textContent = data.username;
                        document.getElementById('modal-password').textContent = data.password;
                        document.getElementById('password-modal').classList.remove('hidden');
                    } else {
                        window.location.href = '{{ route("users.index") }}';
                    }
                } else {
                    if (data.errors) {
                        let errorMessage = 'Validation errors:\n';
                        for (const [field, errors] of Object.entries(data.errors)) {
                            errorMessage += `${field}: ${errors.join(', ')}\n`;
                        }
                        alert(errorMessage);
                    } else {
                        alert(data.message || 'An error occurred while creating the user');
                    }
                    submitBtn.disabled = false;
                    submitSpinner.classList.add('hidden');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while creating the user');
                submitBtn.disabled = false;
                submitSpinner.classList.add('hidden');
            });
        });

        function copyToClipboard(elementId) {
            const element = document.getElementById(elementId);
            const text = element.textContent;
            
            navigator.clipboard.writeText(text).then(function() {
                // Show a temporary success message
                const originalText = element.textContent;
                element.textContent = 'Copied!';
                element.classList.add('text-green-600');
                
                setTimeout(() => {
                    element.textContent = originalText;
                    element.classList.remove('text-green-600');
                }, 1000);
            }).catch(function(err) {
                console.error('Could not copy text: ', err);
                alert('Failed to copy to clipboard');
            });
        }

        function closePasswordModal() {
            document.getElementById('password-modal').classList.add('hidden');
            window.location.href = '{{ route("users.index") }}';
        }
    </script>
</x-app-layout>