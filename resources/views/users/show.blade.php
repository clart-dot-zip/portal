<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('User Details') }}: {{ $authentikUser['username'] }}
            </h2>
            <a href="{{ route('users.index') }}" 
               class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Back to Users
            </a>
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
</x-app-layout>