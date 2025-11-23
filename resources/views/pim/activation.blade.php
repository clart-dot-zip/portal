@section('title', 'PIM Activation - ' . config('app.name'))

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">PIM Activation</h2>
            <p class="text-sm text-gray-600">Review the groups you are eligible to activate and request time-boxed access when needed.</p>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
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

            <div class="bg-white shadow-sm rounded-lg p-6 flex flex-wrap gap-4">
                <div class="flex items-center gap-3">
                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-full {{ $pimEnabled ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.707a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </span>
                    <div>
                        <p class="text-sm text-gray-500">PIM Feature</p>
                        <p class="font-semibold text-gray-900">{{ $pimEnabled ? 'Enabled' : 'Disabled' }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-full {{ $pimOperational ? 'bg-blue-100 text-blue-700' : 'bg-yellow-100 text-yellow-700' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 3a1 1 0 11-2 0 1 1 0 012 0zm-.25-6.75a.75.75 0 00-1.5 0v4.5a.75.75 0 001.5 0v-4.5z" clip-rule="evenodd" />
                        </svg>
                    </span>
                    <div>
                        <p class="text-sm text-gray-500">Configuration</p>
                        <p class="font-semibold text-gray-900">{{ $pimOperational ? 'Ready' : 'Needs Setup' }}</p>
                    </div>
                </div>
            </div>

            @if(!$pimEnabled)
                <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 px-4 py-3 rounded-lg">
                    PIM is currently disabled by the portal administrators. Reach out to the platform team if you believe this is unexpected.
                </div>
            @elseif(!$pimOperational)
                <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 px-4 py-3 rounded-lg">
                    PIM groups or permissions are not configured yet. Check back later once an administrator sets up your assignments.
                </div>
            @elseif(!$currentUser)
                <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
                    You must be signed in to request privileged access.
                </div>
            @else
                <div class="bg-white shadow-sm rounded-lg p-6 space-y-6">
                    <div class="flex flex-col gap-2">
                        <h3 class="text-lg font-semibold text-gray-900">Eligible PIM Groups</h3>
                        <p class="text-sm text-gray-600">Select a group below, choose an appropriate duration, and explain why you need the access. Requests are auto-approved when your group is configured for it.</p>
                    </div>

                    @if($groups->isEmpty())
                        <div class="bg-gray-50 border border-dashed border-gray-200 rounded-lg p-6 text-center text-sm text-gray-500">
                            You are not assigned to any PIM groups yet. Ask an administrator to grant you access to the workflows you need.
                        </div>
                    @else
                        <div class="space-y-5">
                            @foreach($groups as $groupData)
                                @php
                                    /** @var \App\Models\PimGroup $group */
                                    $group = $groupData['group'];
                                    $activation = $groupData['active_activation'];
                                    $permissions = $groupData['permissions'];
                                @endphp
                                <div class="border border-gray-200 rounded-xl p-5 space-y-4">
                                    <div class="flex flex-wrap items-start justify-between gap-4">
                                        <div>
                                            <p class="text-base font-semibold text-gray-900">{{ $group->name }}</p>
                                            <p class="text-sm text-gray-600">{{ $group->description ?? 'No description provided.' }}</p>
                                            <p class="mt-2 text-xs text-gray-500">Duration window: {{ $groupData['minimum_duration_minutes'] }}-{{ $groupData['max_duration_minutes'] }} minutes (default {{ $groupData['default_duration_minutes'] }} minutes)</p>
                                        </div>
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $activation ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}">
                                            {{ $activation ? 'Active' : 'Ready to Activate' }}
                                        </span>
                                    </div>

                                    <div>
                                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Included Permissions</p>
                                        <div class="flex flex-wrap gap-2">
                                            @forelse($permissions as $permission)
                                                <span class="px-2 py-1 rounded-full text-xs bg-gray-100 text-gray-700">{{ $permission->label ?? \Illuminate\Support\Str::headline($permission->key) }}</span>
                                            @empty
                                                <span class="text-xs text-gray-500">No permissions listed.</span>
                                            @endforelse
                                        </div>
                                    </div>

                                    @if($activation)
                                        <div class="bg-green-50 border border-green-200 rounded-lg p-4 text-sm text-green-900">
                                            <div class="flex flex-wrap justify-between gap-4">
                                                <div>
                                                    <p class="font-semibold">Active window</p>
                                                    <p>Started {{ optional($activation->activated_at)->format('M j, Y g:i A') ?? '—' }}</p>
                                                    <p>Expires {{ optional($activation->expires_at)->format('M j, Y g:i A') ?? '—' }} ({{ optional($activation->expires_at)->diffForHumans() }})</p>
                                                </div>
                                                <div class="text-sm text-green-800">
                                                    <p class="font-semibold">Reason</p>
                                                    <p>{{ $activation->reason ?? 'No reason provided' }}</p>
                                                </div>
                                            </div>
                                        </div>
                                        <form method="POST" action="{{ route('pim.activation.deactivate', ['activation' => $activation->id]) }}" class="space-y-3">
                                            @csrf
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700" for="self_deactivate_reason_{{ $activation->id }}">Reason (optional)</label>
                                                <input id="self_deactivate_reason_{{ $activation->id }}" name="reason" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm" placeholder="Why are you ending the session?">
                                            </div>
                                            <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                                End Access
                                            </button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('pim.activation.store') }}" class="space-y-4">
                                            @csrf
                                            <input type="hidden" name="pim_group_id" value="{{ $group->id }}">
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700" for="duration_{{ $group->id }}">Duration (minutes)</label>
                                                    <input id="duration_{{ $group->id }}"
                                                        name="duration_minutes"
                                                        type="number"
                                                        min="{{ $groupData['minimum_duration_minutes'] }}"
                                                        max="{{ $groupData['max_duration_minutes'] }}"
                                                        value="{{ $groupData['default_duration_minutes'] }}"
                                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                                        required>
                                                    <p class="mt-1 text-xs text-gray-500">Allowed window: {{ $groupData['minimum_duration_minutes'] }}-{{ $groupData['max_duration_minutes'] }} minutes.</p>
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700" for="reason_{{ $group->id }}">Reason</label>
                                                    <textarea id="reason_{{ $group->id }}" name="reason" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" placeholder="Describe what you will be doing" required></textarea>
                                                </div>
                                            </div>
                                            <div class="flex justify-end">
                                                <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                    Activate Access
                                                </button>
                                            </div>
                                        </form>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="bg-white shadow-sm rounded-lg p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Recent Activations</h3>
                            <p class="text-sm text-gray-600">Track your last requests for auditing purposes.</p>
                        </div>
                        @if($recentActivations->isNotEmpty())
                            <span class="text-xs font-semibold text-gray-500">Showing {{ $recentActivations->count() }} latest</span>
                        @endif
                    </div>
                    @if($recentActivations->isEmpty())
                        <p class="text-sm text-gray-500">No activation history yet.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 text-sm">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-2 text-left font-medium text-gray-500">Group</th>
                                        <th class="px-4 py-2 text-left font-medium text-gray-500">Status</th>
                                        <th class="px-4 py-2 text-left font-medium text-gray-500">Window</th>
                                        <th class="px-4 py-2 text-left font-medium text-gray-500">Reason</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 bg-white">
                                    @foreach($recentActivations as $history)
                                        @php
                                            $status = $history->status;
                                            if ($status === 'active' && $history->deactivated_at) {
                                                $status = 'revoked';
                                            }
                                            $badgeClasses = [
                                                'active' => 'bg-green-100 text-green-800',
                                                'pending' => 'bg-yellow-100 text-yellow-800',
                                                'revoked' => 'bg-blue-100 text-blue-800',
                                                'expired' => 'bg-purple-100 text-purple-800',
                                                'failed' => 'bg-red-100 text-red-800',
                                            ][$status] ?? 'bg-gray-100 text-gray-600';
                                        @endphp
                                        <tr>
                                            <td class="px-4 py-3">
                                                <div class="font-medium text-gray-900">{{ $history->pimGroup?->name ?? 'Unknown group' }}</div>
                                                <div class="text-xs text-gray-500">{{ $history->duration_minutes }} minutes</div>
                                            </td>
                                            <td class="px-4 py-3">
                                                <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $badgeClasses }}">{{ ucfirst($status) }}</span>
                                            </td>
                                            <td class="px-4 py-3">
                                                <div>Start: {{ optional($history->activated_at)->format('M j, Y g:i A') ?? '—' }}</div>
                                                <div class="text-xs text-gray-500">End: {{ optional($history->expires_at)->format('M j, Y g:i A') ?? '—' }}</div>
                                            </td>
                                            <td class="px-4 py-3 text-gray-700">
                                                {{ $history->reason ?? '—' }}
                                                @if($history->deactivation_reason)
                                                    <div class="text-xs text-gray-500 mt-1">{{ $history->deactivation_reason }}</div>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
