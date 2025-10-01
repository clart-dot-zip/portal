<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Group Details') }}: {{ $group['name'] }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('groups.edit', $group['pk']) }}" 
                   class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                    Edit Group
                </a>
                <a href="{{ route('groups.index') }}" 
                   class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Back to Groups
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Group Information Card -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Group Information</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <dl class="space-y-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Name</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $group['name'] }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Group ID</dt>
                                    <dd class="mt-1 text-sm text-gray-900 font-mono">{{ $group['pk'] }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">UUID</dt>
                                    <dd class="mt-1 text-sm text-gray-900 font-mono">{{ $group['uuid'] ?? 'Not available' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Parent Group</dt>
                                    <dd class="mt-1 text-sm text-gray-900">
                                        @if(isset($group['parent_name']) && $group['parent_name'])
                                            {{ $group['parent_name'] }}
                                        @else
                                            <span class="text-gray-400">Root Group</span>
                                        @endif
                                    </dd>
                                </div>
                            </dl>
                        </div>
                        
                        <div>
                            <dl class="space-y-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Superuser Group</dt>
                                    <dd class="mt-1">
                                        @if($group['is_superuser'] ?? false)
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                                Yes
                                            </span>
                                        @else
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                No
                                            </span>
                                        @endif
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Member Count</dt>
                                    <dd class="mt-1 text-sm text-gray-900">
                                        @if(isset($group['users']) && is_array($group['users']))
                                            {{ count($group['users']) }} users
                                        @else
                                            {{ count($members) }} users
                                        @endif
                                    </dd>
                                </div>
                                @if(isset($group['attributes']) && is_array($group['attributes']) && count($group['attributes']) > 0)
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Custom Attributes</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ count($group['attributes']) }} attributes</dd>
                                    </div>
                                @endif
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Group Members -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Group Members</h3>
                        <a href="{{ route('groups.edit', $group['pk']) }}" 
                           class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-sm">
                            Manage Members
                        </a>
                    </div>
                    
                    @if(count($members) > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($members as $member)
                                <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50">
                                    <div class="flex items-center justify-between">
                                        <div class="flex-1">
                                            <h4 class="font-medium text-gray-900">{{ $member['username'] }}</h4>
                                            @if(isset($member['name']) && $member['name'])
                                                <p class="text-sm text-gray-600">{{ $member['name'] }}</p>
                                            @endif
                                            @if(isset($member['email']) && $member['email'])
                                                <p class="text-sm text-gray-500">{{ $member['email'] }}</p>
                                            @endif
                                            <div class="mt-2">
                                                @if($member['is_active'] ?? true)
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                        Active
                                                    </span>
                                                @else
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                        Inactive
                                                    </span>
                                                @endif
                                                @if($member['is_superuser'] ?? false)
                                                    <span class="ml-1 px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                                        Superuser
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="flex flex-col space-y-1">
                                            <a href="{{ route('users.show', $member['pk']) }}" 
                                               class="text-xs text-blue-600 hover:text-blue-900">View</a>
                                        </div>
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
                                This group doesn't have any members yet.
                            </p>
                            <div class="mt-6">
                                <a href="{{ route('groups.edit', $group['pk']) }}" 
                                   class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Add Members
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Custom Attributes -->
            @if(isset($group['attributes']) && is_array($group['attributes']) && count($group['attributes']) > 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Custom Attributes</h3>
                        
                        <div class="bg-gray-50 rounded-lg p-4">
                            <pre class="text-sm text-gray-700 whitespace-pre-wrap">{{ json_encode($group['attributes'], JSON_PRETTY_PRINT) }}</pre>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Additional Information -->
            @if(isset($group['roles']) || isset($group['used_by']))
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Additional Information</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @if(isset($group['roles']) && count($group['roles']) > 0)
                                <div>
                                    <h4 class="text-sm font-medium text-gray-900 mb-2">Assigned Roles</h4>
                                    <ul class="space-y-1">
                                        @foreach($group['roles'] as $role)
                                            <li class="text-sm text-gray-600">{{ $role }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            
                            @if(isset($group['used_by']) && count($group['used_by']) > 0)
                                <div>
                                    <h4 class="text-sm font-medium text-gray-900 mb-2">Used By</h4>
                                    <ul class="space-y-1">
                                        @foreach($group['used_by'] as $usage)
                                            <li class="text-sm text-gray-600">{{ $usage['name'] ?? $usage }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>