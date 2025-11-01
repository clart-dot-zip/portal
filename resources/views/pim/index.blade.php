@section('title', 'Privileged Identity Management - ' . config('app.name'))

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Privileged Identity Management') }}
                </h2>
                <p class="mt-1 text-sm text-gray-600">
                    Monitor and control just-in-time privileged access across linked server accounts.
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
            @elseif(!$pimOperational)
                <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 px-4 py-3 rounded-lg">
                    Server connectivity is incomplete. Validate SSH credentials, sudo configuration, and identity files before issuing new activations.
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
                    <h3 class="text-lg font-medium text-gray-900">Available Roles</h3>
                    <p class="text-sm text-gray-500 mt-1">Reference for the roles that can be elevated along with their guardrails.</p>
                </div>
                <div class="divide-y divide-gray-100">
                    @forelse($roleCatalog as $role)
                        <div class="p-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                            <div>
                                <div class="text-md font-semibold text-gray-900">{{ $role['label'] ?? ucfirst($role['key']) }}</div>
                                <div class="text-sm text-gray-500 mt-1">{{ $role['description'] ?? 'No description provided.' }}</div>
                                <div class="mt-2 text-sm text-gray-500">
                                    Target group: <span class="font-medium text-gray-700">{{ $role['group'] ?? 'Not configured' }}</span>
                                </div>
                            </div>
                            <div class="flex flex-wrap gap-3 text-sm text-gray-600">
                                <span class="inline-flex items-center px-3 py-1 rounded-full bg-gray-100">Default {{ (int) ($role['default_duration_minutes'] ?? 15) }} min</span>
                                <span class="inline-flex items-center px-3 py-1 rounded-full bg-green-100">Max {{ (int) ($role['max_duration_minutes'] ?? 60) }} min</span>
                                <span class="inline-flex items-center px-3 py-1 rounded-full bg-blue-100">Min {{ (int) ($role['minimum_duration_minutes'] ?? 5) }} min</span>
                            </div>
                        </div>
                    @empty
                        <div class="p-6 text-sm text-gray-500">No PIM roles are configured. Update <code>config/pim.php</code> to add role definitions.</div>
                    @endforelse
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
                            <input id="search" name="search" type="text" value="{{ $search }}" placeholder="Search by user, server username, role, or reason" class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
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
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">Server Username</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">Role</th>
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
                                            $roleDefinition = $roleCatalog->get($activation->role);
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
                                                {{ $activation->server_username_snapshot ?? ($user->server_username ?? '-') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                <div class="font-medium text-gray-900">{{ $roleDefinition['label'] ?? ucfirst($activation->role) }}</div>
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
