@section('title', 'Privileged Identity Management - ' . config('app.name'))

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Privileged Identity Management') }}
                </h2>
                <p class="mt-1 text-sm text-gray-600">
                    Monitor and control just-in-time access to sensitive portal features and workflows.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $pimEnabled ? 'bg-green-100 text-green-800' : 'bg-gray-200 text-gray-600' }}">
                    {{ $pimEnabled ? 'PIM Enabled' : 'PIM Disabled' }}
                </span>
                @if($pimEnabled)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $pimOperational ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800' }}">
                        {{ $pimOperational ? 'Operational' : 'Configuration Required' }}
                    </span>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif

            @if(!$pimEnabled)
                <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 px-4 py-3 rounded-lg">
                    Privileged access is currently disabled. Update the PIM environment variables to enable activations.
                </div>
            @endif

            @php
                $summaryLabels = [
                    'total' => ['label' => 'Total Records', 'classes' => 'bg-gray-100 text-gray-700'],
                    'active' => ['label' => 'Active', 'classes' => 'bg-green-100 text-green-800'],
                    'pending' => ['label' => 'Pending', 'classes' => 'bg-yellow-100 text-yellow-800'],
                    'failed' => ['label' => 'Failed', 'classes' => 'bg-red-100 text-red-800'],
                    'revoked' => ['label' => 'Revoked', 'classes' => 'bg-blue-100 text-blue-800'],
                    'expired' => ['label' => 'Expired', 'classes' => 'bg-purple-100 text-purple-800'],
                ];
            @endphp

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">
                @foreach($summaryLabels as $key => $data)
                    <div class="bg-white shadow-sm rounded-lg p-4">
                        <div class="text-xs uppercase tracking-wide text-gray-500">{{ $data['label'] }}</div>
                        <div class="mt-2 flex items-baseline justify-between">
                            <span class="text-2xl font-semibold text-gray-900">{{ number_format($summary[$key] ?? 0) }}</span>
                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $data['classes'] }}">
                                {{ $data['label'] }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">PIM Groups &amp; Permissions</h3>
                    <p class="text-sm text-gray-500 mt-1">Define the groups that can be activated, along with their duration limits and portal permissions.</p>
                </div>
                <div class="p-6 space-y-8">
                    <div>
                        <h4 class="text-base font-semibold text-gray-900 mb-3">Create a New Group</h4>
                        <form method="POST" action="{{ route('pim.groups.store') }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @csrf
                            <div>
                                <label for="group_name" class="block text-sm font-medium text-gray-700">Name</label>
                                <input type="text" id="group_name" name="name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                            </div>
                            <div>
                                <label for="group_slug" class="block text-sm font-medium text-gray-700">Slug (optional)</label>
                                <input type="text" id="group_slug" name="slug" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="e.g., git-management">
                            </div>
                            <div class="md:col-span-2">
                                <label for="group_description" class="block text-sm font-medium text-gray-700">Description</label>
                                <textarea id="group_description" name="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                            </div>
                            <div>
                                <label for="group_min_duration" class="block text-sm font-medium text-gray-700">Minimum Duration (minutes)</label>
                                <input type="number" id="group_min_duration" name="min_duration_minutes" value="5" min="1" max="1440" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                            </div>
                            <div>
                                <label for="group_default_duration" class="block text-sm font-medium text-gray-700">Default Duration (minutes)</label>
                                <input type="number" id="group_default_duration" name="default_duration_minutes" value="15" min="1" max="1440" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                            </div>
                            <div>
                                <label for="group_max_duration" class="block text-sm font-medium text-gray-700">Maximum Duration (minutes)</label>
                                <input type="number" id="group_max_duration" name="max_duration_minutes" value="60" min="1" max="1440" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                            </div>
                            <div class="md:col-span-2">
                                <label for="group_permissions" class="block text-sm font-medium text-gray-700">Permissions</label>
                                <select id="group_permissions" name="permissions[]" multiple class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 h-40">
                                    @foreach($permissions as $permission)
                                        <option value="{{ $permission->id }}">{{ $permission->label ?? \Illuminate\Support\Str::headline($permission->key) }}</option>
                                    @endforeach
                                </select>
                                <p class="text-xs text-gray-500 mt-1">Hold Ctrl (Windows) or Command (macOS) to select multiple permissions.</p>
                            </div>
                            <div class="flex items-center space-x-2">
                                <input id="group_auto_approve" name="auto_approve" type="checkbox" value="1" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <label for="group_auto_approve" class="text-sm text-gray-700">Auto-approve activations for this group</label>
                            </div>
                            <div class="md:col-span-2 flex justify-end">
                                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md transition-colors duration-200">Create Group</button>
                            </div>
                        </form>
                    </div>
                    <div>
                        <h4 class="text-base font-semibold text-gray-900 mb-3">Existing Groups</h4>
                        @forelse($groups as $group)
                            <details class="border border-gray-200 rounded-lg mb-4" @if($loop->first) open @endif>
                                <summary class="px-4 py-3 cursor-pointer flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $group->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $group->permissions->count() }} permissions · Default {{ $group->default_duration_minutes }} min</p>
                                    </div>
                                    <span class="text-xs font-semibold text-gray-500">Edit</span>
                                </summary>
                                <div class="px-4 pb-4 pt-2 space-y-4">
                                    <form method="POST" action="{{ route('pim.groups.update', $group) }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        @csrf
                                        @method('PUT')
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Name</label>
                                            <input type="text" name="name" value="{{ $group->name }}" class="mt-1 block w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500" required>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Slug</label>
                                            <input type="text" name="slug" value="{{ $group->slug }}" class="mt-1 block w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                                        </div>
                                        <div class="md:col-span-2">
                                            <label class="block text-sm font-medium text-gray-700">Description</label>
                                            <textarea name="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500">{{ $group->description }}</textarea>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Min Duration</label>
                                            <input type="number" name="min_duration_minutes" value="{{ $group->min_duration_minutes }}" class="mt-1 block w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500" required>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Default Duration</label>
                                            <input type="number" name="default_duration_minutes" value="{{ $group->default_duration_minutes }}" class="mt-1 block w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500" required>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Max Duration</label>
                                            <input type="number" name="max_duration_minutes" value="{{ $group->max_duration_minutes }}" class="mt-1 block w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500" required>
                                        </div>
                                        <div class="md:col-span-2">
                                            <label class="block text-sm font-medium text-gray-700">Permissions</label>
                                            <select name="permissions[]" multiple class="mt-1 block w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500 h-32">
                                                @foreach($permissions as $permission)
                                                    <option value="{{ $permission->id }}" @if($group->permissions->contains('id', $permission->id)) selected @endif>
                                                        {{ $permission->label ?? \Illuminate\Support\Str::headline($permission->key) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            <input type="checkbox" name="auto_approve" value="1" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" @checked($group->auto_approve)>
                                            <span class="text-sm text-gray-700">Auto-approve activations</span>
                                        </div>
                                        <div class="md:col-span-2 flex justify-end gap-3">
                                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md">Save Changes</button>
                                        </div>
                                    </form>
                                    <form method="POST" action="{{ route('pim.groups.destroy', $group) }}" onsubmit="return confirm('Delete this group? Activations referencing it will be removed.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-sm text-red-600 hover:text-red-800 font-medium">Delete Group</button>
                                    </form>
                                </div>
                            </details>
                        @empty
                            <p class="text-sm text-gray-500">No PIM groups are configured yet.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Activation History</h3>
                    <p class="text-sm text-gray-500 mt-1">Search and filter existing activations to audit elevated access.</p>
                </div>
                <div class="p-6 border-b border-gray-200">
                    <form method="GET" action="{{ route('pim.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="md:col-span-3">
                            <label for="search" class="block text-sm font-medium text-gray-700">Search</label>
                            <input id="search" name="search" type="text" value="{{ $search }}" placeholder="Search by user, group, or reason" class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                            <select id="status" name="status" class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                @foreach($statusOptions as $value => $label)
                                    <option value="{{ $value }}" @if(($value === '' && $currentStatus === null) || $currentStatus === $value) selected @endif>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="md:col-span-4 flex gap-3">
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md transition-colors duration-200">Apply Filters</button>
                            <a href="{{ route('pim.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium py-2 px-4 rounded-md transition-colors duration-200">Clear</a>
                        </div>
                    </form>
                </div>
                <div class="p-6">
                    @php
                        $statusColors = [
                            'active' => 'bg-green-100 text-green-800',
                            'pending' => 'bg-yellow-100 text-yellow-800',
                            'failed' => 'bg-red-100 text-red-800',
                            'revoked' => 'bg-blue-100 text-blue-800',
                            'expired' => 'bg-purple-100 text-purple-800',
                        ];
                    @endphp

                    @if($activations->count() === 0)
                        <div class="text-sm text-gray-500">No activations found for the selected filters.</div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">User</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">Group</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">Reason</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">Window</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($activations as $activation)
                                        @php
                                            $statusKey = $activation->status;
                                            if ($statusKey === 'active' && $activation->deactivated_at) {
                                                $statusKey = 'revoked';
                                            }
                                            $badgeClass = $statusColors[$statusKey] ?? 'bg-gray-100 text-gray-700';
                                            $user = $activation->user;
                                        @endphp
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $badgeClass }}">
                                                    {{ ucfirst($statusKey) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                @if($user)
                                                    <div class="font-medium text-gray-900">{{ $user->name ?? $user->username }}</div>
                                                    <div class="text-gray-500 text-xs">{{ $user->email }}</div>
                                                @else
                                                    <div class="text-gray-500">User record missing</div>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                <div class="font-medium text-gray-900">{{ $activation->pimGroup?->name ?? 'Unknown Group' }}</div>
                                                <div class="text-xs text-gray-500">Duration {{ $activation->duration_minutes }} min</div>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-700 max-w-xs">
                                                <div>{{ $activation->reason ?? '—' }}</div>
                                                @if($activation->deactivation_reason)
                                                    <div class="text-xs text-gray-500 mt-1">{{ $activation->deactivation_reason }}</div>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                <div>Started {{ optional($activation->activated_at)->format('Y-m-d H:i') ?? '—' }}</div>
                                                <div class="text-xs text-gray-500">Expires {{ optional($activation->expires_at)->diffForHumans(null, true) ?? '—' }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                <div class="flex flex-col gap-2">
                                                    @if($user && $user->authentik_id)
                                                        <a href="{{ route('users.show', ['id' => $user->authentik_id, 'tab' => 'pim']) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">View User</a>
                                                    @endif
                                                    @if($activation->isActive() && $user && $user->authentik_id)
                                                        <form method="POST" action="{{ route('users.pim.deactivate', ['id' => $user->authentik_id, 'activation' => $activation->id]) }}" onsubmit="return confirm('Revoke this activation?');">
                                                            @csrf
                                                            <input type="hidden" name="reason" value="Revoked via PIM dashboard">
                                                            <button type="submit" class="text-red-600 hover:text-red-800 text-sm font-medium">Revoke</button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-6">
                            {{ $activations->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
